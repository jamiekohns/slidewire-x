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

## `slidewire:pdf`

Export a presentation to PDF.

```bash
php artisan slidewire:pdf demo/q1-kickoff --output=storage/app/q1-kickoff.pdf
```

### Signature

```text
slidewire:pdf {presentation} {--output=} {--format=a4} {--orientation=landscape}
```

### Options

- `--output=`: destination file path
- `--format=`: paper size (`a4`, `letter`, ...)
- `--orientation=`: `portrait` or `landscape`

### Notes

- Uses Browsershot to render the deck, so Node.js plus Chrome or Chromium must be available on the system.
- Command returns a clear error when the PDF runtime dependencies are unavailable.
