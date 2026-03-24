#!/usr/bin/env bash
# Installs the WordPress test library so integration tests that extend
# WP_UnitTestCase can run against a real (temporary) WordPress database.
#
# Usage:
#   bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Example:
#   bin/install-wp-tests.sh wordpress_test root '' 127.0.0.1 latest
#
# The script installs WordPress core and the WordPress test library into:
#   /tmp/wordpress/          — WordPress installation
#   /tmp/wordpress-tests-lib/ — Test library (includes WP_UnitTestCase)
#
# After running this script, configure tests/wp-tests-config.php with the
# same database credentials, then run: vendor/bin/phpunit --testsuite integration

set -ex

DB_NAME=${1:-wordpress_test}
DB_USER=${2:-root}
DB_PASS=${3:-''}
DB_HOST=${4:-localhost}
WP_VERSION=${5:-latest}

WP_TESTS_DIR=${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-/tmp/wordpress}

download() {
    if [ "$(which curl)" ]; then
        curl -s "$1" > "$2"
    elif [ "$(which wget)" ]; then
        wget -nv -O "$2" "$1"
    fi
}

install_wp() {
    if [ -d "$WP_CORE_DIR" ]; then
        return
    fi

    mkdir -p "$WP_CORE_DIR"

    if [ "$WP_VERSION" == 'latest' ]; then
        local ARCHIVE_NAME='latest'
    else
        local ARCHIVE_NAME="wordpress-${WP_VERSION}"
    fi

    download "https://wordpress.org/${ARCHIVE_NAME}.tar.gz" /tmp/wordpress.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"
}

install_test_suite() {
    if [ -d "$WP_TESTS_DIR" ]; then
        return
    fi

    mkdir -p "$WP_TESTS_DIR"

    if [ "$WP_VERSION" == 'latest' ]; then
        local TAG='trunk'
    else
        local TAG="tags/${WP_VERSION}"
    fi

    # Download the test suite from the WordPress svn mirror.
    svn co --quiet --ignore-externals \
        "https://develop.svn.wordpress.org/${TAG}/tests/phpunit/includes/" \
        "${WP_TESTS_DIR}/includes"
    svn co --quiet --ignore-externals \
        "https://develop.svn.wordpress.org/${TAG}/tests/phpunit/data/" \
        "${WP_TESTS_DIR}/data"

    download \
        "https://develop.svn.wordpress.org/${TAG}/wp-tests-config-sample.php" \
        "${WP_TESTS_DIR}/wp-tests-config.php"

    # Replace placeholder values with the provided credentials.
    sed -i "s|youremptytestdbnamehere|${DB_NAME}|" "${WP_TESTS_DIR}/wp-tests-config.php"
    sed -i "s|yourusernamehere|${DB_USER}|" "${WP_TESTS_DIR}/wp-tests-config.php"
    sed -i "s|yourpasswordhere|${DB_PASS}|" "${WP_TESTS_DIR}/wp-tests-config.php"
    sed -i "s|localhost|${DB_HOST}|" "${WP_TESTS_DIR}/wp-tests-config.php"
    sed -i "s|dirname( __FILE__ ) . '/src/'|'${WP_CORE_DIR}/'|" "${WP_TESTS_DIR}/wp-tests-config.php"
}

create_db() {
    mysql -u "$DB_USER" --password="$DB_PASS" -h "$DB_HOST" \
        -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
}

install_wp
install_test_suite
create_db

echo ""
echo "WordPress test environment installed."
echo "Run integration tests with: vendor/bin/phpunit --testsuite integration"
