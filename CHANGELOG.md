# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)


## [7.3.4] - 2023-02-07
### Fixed
- Fixed workspace current call problems


## [7.3.3] - 2023-01-16
### Changed
- Agents can no longer see settings menus
- Agents call listen and download permissions


## [7.3.2] - 2023-01-09
### Fixed
- Fixed recordings category export


## [7.3.1] - 2022-12-31
### Added
- Display categories in recordings export


## [7.3.0] - 2022-12-29
### Added
- Database migration: Add send SMS to config
- Parser can send SMS on EXIT* events
- Added percentage to calls without service
- Added link to callback calls in recordings
- Added ability to mark calls as called back

### Changed
- Fixed recordings export error for managers


## [7.2.1] - 2022-12-22
### Changed
- Auto marking of called_back calls accounts for outbound prefix


## [7.2.0] - 2022-12-22
### Added
- Ability to select app language upon login

### Fixed
- Category fixes

### Changed
- Default period for automatically marking calls as called_back is 480 minutes
- Default language set to Georgian


## [7.1.4] - 2022-12-14
### Fixed
- Fixed navbar in workspace


## [7.1.3] - 2022-12-14
### Fixed
- CSS fixes


## [7.1.2] - 2022-12-14
### Added
- Future event subject_family and subject_comment handling
- Added pause time display

### Fixed
- Fixed incorrect link in header
- Fixed incorrect API URL in workspace
- Fixed error in export for recordings


## [7.1.1] - 2022-11-28
### Added
- Added queue display name in queues/stats page
- Added agent display name in agents/stats page


## [7.1.0] - 2022-11-28
### Changed
- Merged call subject related fixes


## [7.0.4] - 2022-11-21
### Fixed
- Queues/index displaying only user queues
- Fixed agents not visible for managers in monitoring dashboard


## [7.0.3] - 2022-11-06
### Added
- Database migration: Added subjects to qq_calls

### Changed
- Added workspace to new header
- Adjusted routes for agent role, now using unified views and controllers for all roles

### Fiexd
- Fixed refreshing stats not working in start/admin
- Minor fixes in recordings pages

### Removed
- Removed unique calls stats


## [7.0.2] - 2022-11-02
### Changed
- Actually displaying caller count in queue realtime pages
- Displaying caller information in queues/realtime
- Total reskin of monitoring/index

### Removed
- Deleted custom monitoring dashboards


## [7.0.1] - 2022-11-02
### Fixed
- Agent realtime page now show proper count for agents, fixes #481
- Fixed division by zero, closes #484
- Fixed start/manager so it uses new assets

### Removed
- Removed migrattions that were braking clean install process
- Removed all mentions of Call_category_model


## [7.0.0] - 2022-11-01
### Changed
- Start of 7.x version
