# Commands

## `make:slidewire`

Generate a presentation scaffold.

```bash
php artisan make:slidewire demo/q1-kickoff --title="Q1 Kickoff"
```

### Signature

```text
make:slidewire {name?} {--presentation=} {--title=} {--force}
```

### Arguments and Options

- `name` (optional): presentation key/path (e.g. `team/q1-kickoff`)
- `--presentation=`: explicit presentation key (overrides argument)
- `--title=`: heading used in starter slide
- `--force`: overwrite existing starter file

### Interactive Mode

If no argument is passed, the command prompts for:

- presentation path
- presentation title
