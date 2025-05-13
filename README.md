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
composer require baad777/codeingestor --dev
```

Or you can install it globally

```sh
composer global require baad777/codeingestor
```

## Usage

### Configuration

Create a configuration file named `codeingestor.yaml` in the root of your project with the following structure(example):

```yaml
# Default configuration
sourcePath: "./"                            # Folder to scan
outputFile: "codeingestor_output.txt"       # Output file
ignoreDirs:                                 # Directories to skip
  - vendor
  - node_modules
  - .git
ignoreFiles:                                # Files to skip
  - .env
  - .gitignore
  - "*.lock"
```

- `source`: The directory path of your PHP project.
- `output`: The file path where the structured text output will be saved.
- `ignore_dirs`: A list of directories to ignore during the parsing process.

### Command Line Interface

CodeIngestor comes with a command line interface (CLI) tool. You can run it using the following command:

```sh
./vendor/bin/codeingestor /path/to/folder
```

This command will execute the parser and generate the structured text output based on your configuration.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

CodeIngestor is licensed under the MIT License.

## Contact

If you have any questions or need further assistance, feel free to reach out at [adrian@bancu.ro](mailto:adrian@bancu.ro).

---

This README provides a basic overview of CodeIngestor. For more detailed information, please refer to the code documentation and example configurations available in the repository.