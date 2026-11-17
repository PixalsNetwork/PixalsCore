# PixalsCore

**An abandoned Minecraft Bedrock network core built on PocketMine-MP.**

PixalsCore was developed as the central infrastructure and gameplay management layer for Pixals Network. Its purpose was to provide a collection of systems that could operate together to support a multiplayer Minecraft network.

The project represents several years of experimentation, development, architectural decisions, and lessons learned.

It is now **abandoned and no longer actively maintained**.

We are publishing it as an open-source project so other developers can explore its implementation, reuse ideas, learn from its architecture, and potentially build upon it.

> **Important:** PixalsCore is provided as-is. It is not production-ready software, and its functionality, compatibility, and security have not been verified for current server deployments.

---

## Features

The codebase contains implementations of several systems:

### Player Management
- Player data initialization and retrieval.
- Player sessions and online-time tracking.
- Rank and permission management.
- Player profiles and statistics.
- Network currency and progression data.
- Player skin and face processing.

### Social Systems
- Friends and friend requests.
- Player profiles.
- Social interfaces.
- Notification interfaces.
- Clan-related player data integration.

### Cosmetics
- Custom tags.
- Cape management.
- Cosmetic models.
- Model textures and geometry handling.
- Player cosmetic interfaces.

### Network Integration
- Custom packet handling.
- Proxy communication through StarGate-Atlantis.
- Remote command dispatching.
- Permission synchronization.
- Scoreboard synchronization.
- Custom player chat packets.

### Lobby Systems
- Teleporter interface.
- Lobby inventory management.
- Custom NPC entities.
- Floating text and holograms.
- Kill, death, and Elo leaderboards.

### Other Integrations
- Tebex command integration.
- MySQL database integration.
- Asynchronous operations through Await-Generator.
- Custom entity registration.
- Custom command registration.

Not every listed feature should be considered complete or independently usable.

---

## Architecture

PixalsCore was organized into several functional areas.

| Component | Responsibility |
|---|---|
| Pixals | Main plugin entry point |
| PixalsCoreStartup | Initialization and registration |
| PixalsManagers | Core management components |
| PixalsPlayer | Player handling and data loading |
| PSessionSystem | Player session management |
| PixalsQueries | Database query definitions |
| PixalsThreads | Thread-based database initialization |
| PixalsPackets | Custom network packet handling |
| PixalsNPCs | Custom NPC entities and interactions |
| PixalsUIs | Player interfaces |
| PixalsTasks | Scheduled operations |
| CoreHandlers | Additional gameplay handlers |

The project uses several external libraries, including:

- PocketMine-MP
- SOFe Await-Generator
- cooldogedev/libSQL
- Pixals Atlas
- DatabaseManager
- AdvancedCommands
- PMForms
- Simple Packet Handler
- StarGate-Atlantis

Some dependencies are custom or externally maintained projects. Their availability and compatibility are not guaranteed.

---

## Requirements

The supplied project metadata identifies:

- PocketMine-MP API 5.
- MySQL.
- StarGate-Atlantis.
- Composer-managed dependencies.

The included dependency lockfile resolves PocketMine-MP 5.44.3 and Bedrock protocol 1.26.30.

**Compatibility is not guaranteed.** The plugin metadata, dependency versions, and actual runtime environment should be reconciled before attempting deployment.

---

## Installation

The repository does not currently provide a verified, reproducible installation procedure.

A prospective developer should first:

1. Inspect the Composer dependencies.
2. Resolve and verify all external libraries.
3. Configure a dedicated MySQL database.
4. Review the database initialization code.
5. Verify the required StarGate-Atlantis integration.
6. Build and test the plugin against a compatible PocketMine-MP installation.

Do not deploy the supplied build or source code directly to a production server without reviewing its security and compatibility.

---

## Known Design Limitations and Architectural Mistakes

We want to be transparent about the state of this project.

PixalsCore was developed through continuous experimentation. Some architectural decisions were made to solve immediate problems and accelerate development rather than establish the most maintainable long-term design.

We recognized several areas that needed improvement.

Unfortunately, those improvements were never completed.

### 1. Database Architecture

The database layer has several areas that deserve reconsideration.

- Database initialization and schema creation have separate execution paths.
- Connection initialization is invoked more than once during startup.
- Schema initialization runs asynchronously without a robust readiness mechanism.
- Database connection failures do not have sufficiently reliable recovery handling.
- Database configuration and environment selection are tightly coupled to source code.

We intended to improve connection lifecycle management, initialization order, and database configuration.

A more structured database abstraction and explicit connection lifecycle would be worth considering.

### 2. Player Session Management

The session system works with mutable player data stored in memory.

However:

- Sessions are keyed by player names rather than UUIDs.
- Repeating tasks are scheduled during session creation.
- Task lifecycles are not properly associated with individual sessions.
- Multiple players can consequently create redundant scheduled operations.

We intended to consolidate session updates and improve lifecycle management.

A centralized session service with properly managed scheduled operations would be a better direction.

### 3. Task Management

The task management layer is incomplete.

Some task registration logic creates task instances without actually scheduling them.

The database refresh and reconnection functionality also remains incomplete.

We intended to establish a more centralized and reliable task management approach.

### 4. Global State and Coupling

Some components depend heavily on:

- Static plugin instances.
- Globally accessible state.
- Mutable public properties.
- Direct construction of other managers.
- Cross-component dependencies.

This makes certain components difficult to isolate, test, and maintain.

We intended to improve separation of responsibilities and reduce unnecessary coupling.

Dependency injection and clearer service boundaries would be potential alternatives.

### 5. Networking

PixalsCore contains custom packet implementations and relies on StarGate-Atlantis.

Some packet identifiers and integration assumptions are closely tied to the surrounding network implementation.

This introduces compatibility considerations when changing protocol versions or replacing the proxy integration.

A more explicitly versioned protocol and better-defined networking boundaries would be worth considering.

### 6. Incomplete Components

Some components are experimental, unfinished, or contain placeholder implementations.

Examples include parts of the social management layer, database reconnection logic, and testing-related classes.

Developers should inspect individual implementations before relying on them.

---

## Why Was It Abandoned?

PixalsCore was developed for Pixals Network.

Over time, the project accumulated more responsibilities, architectural complexity, and maintenance requirements.

We eventually recognized that continuing development would require additional time and resources that we could no longer dedicate to it.

The project was abandoned before several planned architectural improvements could be implemented.

We are not presenting the codebase as a finished framework.

We acknowledge that there are better architectures, design patterns, libraries, and solutions available for many of the problems we attempted to solve.

**We encourage developers to challenge our decisions rather than blindly reproduce them.**

If you find a better way to implement something, use it.

If you find an architectural mistake, learn from it.

If an existing component is not worth maintaining, replace it.

That is part of the reason we are making this project available.

---

## Project Status

**Abandoned — No Active Development**

- No new features are planned.
- No compatibility guarantees are provided.
- No security maintenance is provided.
- No support or response-time guarantees are provided.
- Existing limitations and bugs may remain.

This repository is preserved as a reference to the work that went into PixalsCore.

---

## Acknowledgements

PixalsCore was built as part of the Pixals Network project.

It represents the work, experimentation, and engineering decisions of everyone who contributed to its development.

We hope it can be useful to other developers, whether as a starting point, a reference, or an example of what to reconsider when building their own infrastructure.

**Technology is built by shipping, learning, and improving.**

Thank you for taking a look at PixalsCore.

---

## License

See the repository's LICENSE file for the applicable licensing terms.

