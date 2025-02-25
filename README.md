# CodeIngestor

CodeIngestor is a library designed to parse PHP projects into structured text files for large language model (LLM) ingestion. This tool helps in extracting important information from your codebase and making it easily accessible for analysis, documentation generation, or other computational tasks.

## Features

- **Parse PHP Projects:** Extracts relevant information from PHP project files.
- **Structured Text Output:** Generates structured text files that can be used as input for LLMs.
- **Configurable Source Path:** Allows you to specify the directory of your PHP project.
- **Ignore Directories and Files:** Option to exclude certain directories and files from processing.

## Installation

You can install CodeIngestor via Composer:

```sh
composer require codeingestor/codeingestor
```

## Usage

### Configuration

Create a configuration file named `codeingestor.yaml` in the root of your project with the following structure(example):

```yaml
source: "./"
output: "llm_output.txt"
ignore_dirs:
  - "vendor"
ignore_files:
  - "*.log"
```

- `source`: The directory path of your PHP project.
- `output`: The file path where the structured text output will be saved.
- `ignore_dirs`: A list of directories to ignore during the parsing process.

### Command Line Interface

CodeIngestor comes with a command line interface (CLI) tool. You can run it using the following command:

```sh
./bin/codeingestor
```

This command will execute the parser and generate the structured text output based on your configuration.

## Directory Structure

- **src/**: Contains the source code of the library.
    - **FileScanner.php**: Implements the `generateDirectoryTree` method to build a directory tree structure.
    - **FileScannerInterface.php**: Defines the interface for file scanning operations.
    - **ScanConfiguration.php**: Holds configuration settings for the scan process.
- **bin/**: Contains the executable script for running CodeIngestor via CLI.
- **composer.json**: Composer configuration file for managing dependencies and autoloads.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

CodeIngestor is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

If you have any questions or need further assistance, feel free to reach out at [adrian@bancu.ro](mailto:adrian@bancu.ro).

---

This README provides a basic overview of CodeIngestor. For more detailed information, please refer to the code documentation and example configurations available in the repository.