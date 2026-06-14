---
name: phase-implementation
description: Implement a phase from an improvement plan — read scope, create tasks, execute sequentially, update plan, report completion.
---

# Phase Implementation Workflow

Execute a single phase from an improvement plan (e.g., `IMPROVEMENT_PLAN.md`). Used repeatedly across phases 1–6 in this project.

## When to use

- User says "proceed with phase N" or "implement phase X"
- An improvement plan document exists with numbered phases
- Each phase contains multiple discrete tasks

## Procedure

### 1. Read the phase scope

Read the improvement plan to understand what this phase covers. Identify:
- Which tasks belong to this phase
- Dependencies between tasks
- Files that will be affected
- Any project-specific constraints (e.g., local-only demo, no production features)

### 2. Create tasks

Use the `task` tool to create one task per discrete unit of work. Keep tasks focused — one file change or one logical unit per task. Use clear summaries like "Fix SQL injection in Model::assertIdentifier()".

### 3. Execute tasks sequentially

For each task:
1. `task start` — mark it in-progress
2. Read relevant files to understand current state
3. Implement the change (edit/write)
4. Verify if possible (run tests, check syntax)
5. `task done` with an `event_summary` describing what was done

Work through all tasks before moving on.

### 4. Update the improvement plan

After all tasks in the phase are done:
1. Read the improvement plan
2. Add ✅ marks to completed items
3. Add a phase completion summary (e.g., "### Phase N: Name ✅ COMPLETED")

### 5. Report completion

Provide a summary table:

| Task | Status | Summary |
|------|--------|---------|
| T1: ... | Done | One-line description |
| T2: ... | Done | One-line description |

## Tips

- Batch file reads in parallel when exploring the codebase
- If a task is blocked by another, note the dependency and continue with what you can
- Update the improvement plan incrementally — don't wait until all phases are done
- When the user gives project constraints (e.g., "local-only demo"), filter the phase's tasks through those constraints — skip or simplify what doesn't apply
