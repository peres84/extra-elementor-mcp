# Known Issues

Unresolved problems encountered during development. Remove entries once fixed.

## 2026-03-24 — Remote agent push rejected (resolved)
- **Description:** Scheduled remote agent committed work but `git push` was rejected because the remote had newer commits.
- **Cause:** Agent did not `git pull --rebase` before pushing.
- **Resolution:** Updated trigger prompt to include `git pull --rebase` before every push. Added git workflow section to CLAUDE.md.
