# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.5.0] - 2023-12-28

### Added

- Added `private sendRequest()` method to `Client` to abstract processing HTTP responses in central palce
- Added more specific exceptions in various cases to avoid using only `Exception` class.
- Added `traceId` into error responses from Bank iD and some success responses that are also traceable
- Added information about available support by [Unnits.com](www.unnits.com)

### Changed

- Updated installation instructions.
