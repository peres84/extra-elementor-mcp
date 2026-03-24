# Automated Build Process

This project uses a **scheduled Claude Code remote agent** to implement the plugin incrementally. The agent runs in Anthropic's cloud infrastructure — no local machine required.

## How It Works

1. A **remote trigger** runs every hour on a cron schedule
2. Each run spawns a fresh Claude Code session in the cloud
3. The agent clones the repo, reads `TODO.md` and `CLAUDE.md`, picks the next unchecked task group
4. It implements that group following the patterns in `samples/` and the PRD
5. It marks completed items in `TODO.md`, commits, and pushes
6. The session ends — next hour, a new clean session picks up where it left off

## Trigger Details

| Field | Value |
|-------|-------|
| Name | `extra-elementor-mcp-builder` |
| Trigger ID | `trig_01M2ST81cqnNYdRsFRJoEZoo` |
| Schedule | `0 */1 * * *` (every hour) |
| Model | `claude-sonnet-4-6` |
| Environment | `claude-code-default` |
| Repo | `https://github.com/peres84/extra-elementor-mcp` |

## Managing the Trigger

- **Dashboard:** https://claude.ai/code/scheduled/trig_01M2ST81cqnNYdRsFRJoEZoo
- **Disable/delete:** Visit https://claude.ai/code/scheduled

## Monitoring Progress

Check implementation progress by looking at `TODO.md` — completed items are marked `[x]`.

```bash
# Pull latest and check progress
git pull
cat TODO.md | grep -c '\[x\]'  # completed tasks
cat TODO.md | grep -c '\[ \]'  # remaining tasks
```

## Why This Approach

- **Clean context per task** — Each session starts fresh, so the agent never runs out of context window
- **Incremental commits** — Progress is saved after each group, so nothing is lost if a session fails
- **No local resources** — Runs in Anthropic's cloud, no machine needs to stay on
- **Self-tracking** — `TODO.md` serves as the coordination mechanism between sessions

## Restarting or Modifying

To change the schedule or prompt, update the trigger via Claude Code:

```
# In a Claude Code session:
# "Update my extra-elementor-mcp-builder trigger to run every 2 hours"
# "Disable the builder trigger"
# "Run the builder trigger now"
```

Or manage directly at https://claude.ai/code/scheduled
