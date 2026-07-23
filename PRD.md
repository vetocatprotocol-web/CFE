# Columnar Forensics Engine (CFE)
## Product Requirements & Technical Specification Document
### Version 1.0 — Single Source of Truth

> This document is the absolute authority for all implementation decisions on this project. Any code, design, or architecture that contradicts this document is considered incorrect and must be revised to conform to it. This applies equally to human developers and AI coding assistants (Claude Code, GitHub Copilot, OpenAI Codex, or any other tool).

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Vision](#2-vision)
3. [Philosophy](#3-philosophy)
4. [Design Goals](#4-design-goals)
5. [Non-Goals](#5-non-goals)
6. [Functional Requirements](#6-functional-requirements)
7. [Non-Functional Requirements](#7-non-functional-requirements)
8. [Overall Architecture](#8-overall-architecture)
9. [Component Diagram](#9-component-diagram)
10. [Data Flow](#10-data-flow)
11. [Module Architecture](#11-module-architecture)
12. [Evidence Engine](#12-evidence-engine)
13. [Storage Layer](#13-storage-layer)
14. [SIMD & Parallel Engine](#14-simd--parallel-engine)
15. [Query Planner & Optimizer](#15-query-planner--optimizer)
16. [Streaming Parser Framework](#16-streaming-parser-framework)
17. [Artifact Parsers](#17-artifact-parsers)
18. [Timeline Engine](#18-timeline-engine)
19. [Correlation Engine](#19-correlation-engine)
20. [Rule Engine](#20-rule-engine)
21. [Report Engine](#21-report-engine)
22. [Plugin Architecture](#22-plugin-architecture)
23. [CLI](#23-cli)
24. [GUI](#24-gui)
25. [Configuration](#25-configuration)
26. [Logging, Metrics & Benchmarking](#26-logging-metrics--benchmarking)
27. [Testing & Validation Strategy](#27-testing--validation-strategy)
28. [Performance Targets](#28-performance-targets)
29. [Security & Threat Model](#29-security--threat-model)
30. [Error Handling](#30-error-handling)
31. [API Design](#31-api-design)
32. [Internal Data Format & Universal Artifact Schema](#32-internal-data-format--universal-artifact-schema)
33. [Memory Management](#33-memory-management)
34. [Scheduler & Threading Model](#34-scheduler--threading-model)
35. [Disk I/O, Zero-Copy & Memory Mapping](#35-disk-io-zero-copy--memory-mapping)
36. [Storage Format & Index Strategy](#36-storage-format--index-strategy)
37. [Search Engine](#37-search-engine)
38. [Export Formats (CASE, JSON, CSV, HTML, PDF)](#38-export-formats)
39. [Roadmap & Milestones](#39-roadmap--milestones)
40. [Risks](#40-risks)
41. [Decision Records (ADR)](#41-decision-records)
42. [Coding Standards & Rust Guidelines](#42-coding-standards--rust-guidelines)
43. [Cargo Workspace Structure](#43-cargo-workspace-structure)
44. [Dependency Policy](#44-dependency-policy)
45. [CI/CD](#45-cicd)
46. [Documentation Standards](#46-documentation-standards)
47. [Contribution Guide](#47-contribution-guide)
48. [Glossary](#48-glossary)

---

## 1. Executive Summary

Columnar Forensics Engine (CFE) is a high-performance Digital Evidence Analytics Engine written entirely in Rust. It is not a parser collection; it is an analytical database purpose-built for digital forensics and incident response (DFIR) workloads, applying columnar storage, bitmap indexing, and SIMD-accelerated query execution to forensic artifacts at a scale that traditional row-oriented forensic tools cannot handle efficiently.

CFE ingests raw evidence (disk images, memory images, exported artifact files) through a streaming parsing layer, normalizes every artifact into a Universal Artifact Schema, and persists the result as immutable, columnar, Arrow/Parquet-backed evidence stores. Analysts query this store using a cost-based query engine with predicate/projection pushdown and bitmap-indexed filtering, producing timelines, correlations, and rule-based detections deterministically and reproducibly.

Every decision in this document is final unless superseded by a recorded Architecture Decision Record (ADR, Section 41). The system contains no machine learning, no LLM inference, and no unexplainable heuristics — every transformation from raw bytes to final report must be traceable, deterministic, and independently reproducible by a third party given the same input evidence and the same CFE version.

## 2. Vision

CFE exists to close the performance and scalability gap between the volume of evidence modern investigations produce (multi-terabyte disk images, millions of registry keys, tens of millions of event log records, browser histories spanning years) and the row-oriented, single-threaded tooling that dominates the DFIR ecosystem today.

The long-term vision is an evidence analytics engine that:

- Treats forensic artifacts as structured, typed, columnar data rather than opaque parsed records.
- Answers investigative questions ("show all process creation events correlated with this USB insertion, ordered by time, across three custodians") in seconds rather than hours.
- Produces evidence that is court-defensible: immutable, hash-verified, chain-of-custody tracked, and reproducible bit-for-bit given the same inputs.
- Scales linearly with additional cores and additional evidence sources without requiring a re-architecture.

## 3. Philosophy

Parsing is the least interesting part of a forensic engine. The interesting part is what happens after parsing: storage layout, indexing, and query execution. CFE is architected around this pipeline:

```
Evidence
   │
   ▼
Streaming Parsing        (bounded memory, resumable, per-artifact)
   │
   ▼
Normalization             (raw structs → Universal Artifact Schema)
   │
   ▼
Columnar Storage          (Arrow in-memory, Parquet on-disk)
   │
   ▼
Bitmap Index              (Roaring Bitmaps over dictionary-encoded columns)
   │
   ▼
SIMD Query Engine         (vectorized predicate evaluation, batch-at-a-time)
   │
   ▼
Correlation Engine        (cross-artifact join on entity keys: PID, SID, MAC, hash)
   │
   ▼
Timeline                  (unified, sorted, source-annotated event stream)
   │
   ▼
Reporting                 (CASE / JSON / CSV / HTML / PDF)
```

Design principle: **every stage is independently testable, independently benchmarkable, and independently replaceable.** A parser can be rewritten without touching the query engine. The query engine can be rewritten without touching parsers. This is enforced through the Universal Artifact Schema (Section 32) acting as the sole contract between stages.

Determinism principle: given identical input bytes and an identical CFE version/config, every output (columnar file bytes, query result ordering, report content) must be byte-for-byte reproducible. No wall-clock-dependent, thread-scheduling-dependent, or hash-seed-dependent randomness may leak into any persisted or reported artifact.

## 4. Design Goals

| Goal | Description |
|---|---|
| Speed | Ingest and query at multi-GB/s throughput per node using columnar batch processing and SIMD. |
| Accuracy | Every parser is validated against known-good reference corpora; no silent data loss. |
| Determinism | Identical inputs always produce identical outputs, at the byte level for storage and the row-order level for queries. |
| Auditability | Every transformation is logged with inputs, outputs, and the code path (function + version) that produced it. |
| Immutable Evidence | Once ingested, raw evidence and derived columnar stores are write-once; corrections are appended as new, linked revisions, never in-place mutations. |
| Scalability | Linear scaling across cores (data parallelism) and across machines (partitioned evidence stores, no single-node bottleneck for the storage layer). |
| Modularity | Parsers, storage, query engine, and reporting are separate crates with narrow, versioned interfaces. |
| Reproducibility | A second examiner running the same CFE version against the same evidence gets the same findings. |

## 5. Non-Goals

CFE explicitly does **not**:

- Use machine learning, statistical inference, or LLMs for any artifact interpretation, anomaly detection, or triage decision. All "smart" behavior is expressed as explicit, inspectable rules (Section 20).
- Perform live remote acquisition over a network protocol (out of scope for v1; evidence is provided as images or exported artifact sets — see Roadmap, Section 39, for future extension).
- Attempt malware detonation, dynamic analysis, or sandboxing. CFE is a static, offline analytics engine.
- Provide a general-purpose SQL server; the query surface is deliberately scoped to forensic query patterns (Section 15).
- Guarantee support for proprietary, undocumented artifact formats without a published, versioned parser specification (Section 17) backing the implementation.

## 6. Functional Requirements

FR-1. The system shall ingest evidence containers (raw/E01/AFF4 disk images, plain files, exported artifact directories) and produce a manifest of discovered artifacts.

FR-2. The system shall compute cryptographic hashes (SHA-256 mandatory, MD5 and SHA-1 optional for legacy CASE compatibility) for every ingested source file before parsing.

FR-3. The system shall parse each supported artifact type (Section 17) into the Universal Artifact Schema without loss of source fields; unmapped fields are preserved in a schema-defined `extra` column, never dropped.

FR-4. The system shall persist parsed artifacts as columnar Parquet files partitioned by artifact type, source, and time bucket.

FR-5. The system shall build bitmap indexes over all designated indexable columns (Section 13.4) during ingestion, not as a separate offline step.

FR-6. The system shall answer point, range, and set-membership queries via a query planner that applies predicate pushdown and projection pushdown before any row materialization.

FR-7. The system shall build a unified timeline across all ingested artifacts, sorted by normalized UTC timestamp, with per-event source and confidence-in-timestamp metadata (Section 18.2).

FR-8. The system shall correlate events across artifacts using declared entity keys (process ID + start time composite, SID, MAC address, file hash, USB serial, etc.) via the Correlation Engine (Section 19).

FR-9. The system shall evaluate user-defined, declarative detection rules (Section 20) against the columnar store and produce a rule-hit report.

FR-10. The system shall export findings as CASE/UCO JSON, flat JSON, CSV, HTML, and PDF (Section 38).

FR-11. The system shall record a chain-of-custody log for every evidence item from acquisition through report generation (Section 12.4).

FR-12. The system shall expose all of the above through both a CLI (Section 23) and a stable, versioned library API (Section 31) consumable by a GUI or third-party automation.

## 7. Non-Functional Requirements

| Category | Requirement |
|---|---|
| Performance | Sustained ingest ≥ 500 MB/s per NVMe stream on reference hardware (Section 28); point queries on indexed columns < 50ms at 1B-row scale. |
| Memory | Peak resident memory during ingestion of an N GB evidence source must not exceed 0.15×N plus a fixed 512MB baseline, enforced via streaming parsing and bounded buffer pools (Section 33). |
| Reliability | No parser panic may crash the process; all parser errors are caught, logged, and surfaced as a per-record error entry, never terminate a whole ingestion job. |
| Portability | Core engine builds and runs on Linux x86_64/ARM64 and Windows x86_64 with no OS-specific core logic (OS-specific code confined to acquisition/parser crates). |
| Auditability | 100% of ingestion, query, and export operations are logged in structured (JSON Lines) form with monotonic operation IDs. |
| Determinism | Two runs of `cfe ingest` on identical input, identical config, identical CFE binary version produce byte-identical Parquet files (verified by SHA-256 of output). |
| Security | No dynamic code execution from evidence content; all parsers treat evidence bytes as untrusted input (Section 29). |
| Backward Compatibility | Storage format version N+1 readers must read version N files; breaking changes require a major version bump and a documented migration path (Section 36.3). |

## 8. Overall Architecture

CFE is organized as a Rust Cargo workspace of narrowly-scoped crates (full layout in Section 43). At the highest level there are five architectural planes:

1. **Acquisition Plane** — reads raw evidence containers, verifies hashes, establishes chain of custody.
2. **Parsing Plane** — streaming, artifact-specific parsers that emit Universal Artifact Schema records.
3. **Storage Plane** — columnar (Arrow) in-memory representation, Parquet on-disk persistence, bitmap indexing.
4. **Query & Analytics Plane** — query planner/optimizer, SIMD execution engine, correlation engine, timeline engine, rule engine.
5. **Presentation Plane** — CLI, GUI, report engine, export formats.

Each plane communicates with the next only through explicit, versioned data contracts (Arrow schemas, Parquet files, or Rust trait interfaces) — never through shared mutable state or implicit side channels. This is what allows independent replacement described in Section 3.

## 9. Component Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                          cfe-cli / cfe-gui                          │
└───────────────────────────────┬───────────────────────────────────┘
                                 │  cfe-api (stable library surface)
┌───────────────────────────────┴───────────────────────────────────┐
│                          cfe-orchestrator                          │
│   job scheduling · pipeline wiring · progress reporting            │
└──────┬───────────┬────────────┬─────────────┬──────────┬──────────┘
       │           │            │             │          │
┌──────▼─────┐┌────▼─────┐┌─────▼──────┐┌─────▼─────┐┌───▼────────┐
│ cfe-acquire││cfe-parsers││ cfe-storage││ cfe-query ││cfe-report  │
│ hashing    ││ streaming ││ arrow/     ││ planner/  ││ CASE/JSON/ │
│ custody    ││ artifact  ││ parquet/   ││ optimizer/││ CSV/HTML/  │
│ container  ││ parsers   ││ bitmap idx ││ SIMD exec ││ PDF        │
│ readers    ││ (Sec. 17) ││ (Sec. 13)  ││ (Sec 14/15)││(Sec 21/38)│
└────────────┘└───────────┘└──────┬─────┘└─────┬─────┘└────────────┘
                                   │            │
                             ┌─────▼────────────▼─────┐
                             │   cfe-timeline &        │
                             │   cfe-correlate &       │
                             │   cfe-rules             │
                             │   (Sec 18, 19, 20)      │
                             └─────────────────────────┘
                                        │
                             ┌──────────▼──────────┐
                             │      cfe-schema      │
                             │ Universal Artifact    │
                             │ Schema, shared types  │
                             └───────────────────────┘
```

`cfe-schema` is depended upon by every other crate; it depends on nothing else in the workspace, enforcing it as the single stable contract (Section 32).

## 10. Data Flow

1. **Acquire**: `cfe-acquire` opens the evidence container (raw dd image, E01, AFF4, or a plain directory of exported files), streams it in fixed-size chunks (default 4 MiB), and computes a running SHA-256 digest. The manifest and hash are written to the chain-of-custody log (Section 12.4) before any parsing begins.
2. **Parse**: `cfe-parsers` selects the appropriate artifact parser per discovered file (by signature, not extension — see Section 17.1), streams the file through the parser in bounded-memory batches, and emits `RecordBatch` values conforming to the Universal Artifact Schema (Section 32).
3. **Normalize**: Each parser's raw output type implements `Into<UniversalRecord>`; normalization performs timestamp conversion to UTC nanosecond epoch, path canonicalization, and encoding normalization (UTF-16LE → UTF-8) inline during parsing — not as a separate pass — to avoid an extra full data traversal.
4. **Store**: `cfe-storage` appends normalized `RecordBatch` values to an Arrow `Table`, flushing to partitioned Parquet row groups (Section 13.2) once a configurable batch-size or memory threshold is reached. Dictionary encoding and bitmap indexes (Section 13.4) are built incrementally per row group, not in a second pass.
5. **Index**: Bitmap indexes are persisted alongside Parquet files as sidecar `.cfeidx` files (Section 36), one per indexed column per partition.
6. **Query**: `cfe-query` parses a query (CLI DSL or programmatic `QueryBuilder`, Section 15.1) into a logical plan, applies predicate/projection pushdown, consults bitmap indexes to prune row groups and rows, and executes the remaining predicate evaluation using SIMD-vectorized kernels (Section 14).
7. **Correlate/Timeline**: `cfe-correlate` and `cfe-timeline` run as query-engine consumers, issuing further queries against the stored data to join on entity keys and merge-sort by timestamp respectively.
8. **Rule Evaluation**: `cfe-rules` compiles declarative rule definitions (Section 20.1) into query-engine predicates and correlation graph patterns, executed the same way as ad hoc queries — rules are not a special code path.
9. **Report**: `cfe-report` consumes query/timeline/rule-hit results and renders the requested export format (Section 38), including a fully reconstructed chain-of-custody appendix.

## 11. Module Architecture

Modules are grouped by plane (Section 8). Each module below is documented per the mandatory module template (Purpose, Responsibilities, Inputs, Outputs, Internal Workflow, Dependencies, Performance Target, Error Cases, Future Extension) in its dedicated section (12, 13, 14, 15, 16/17, 18, 19, 20, 21, 22).

Cross-cutting modules that do not belong to a single plane:

### 11.1 `cfe-schema` (Universal Artifact Schema)
- **Purpose**: Define the single normalized data model every artifact is mapped into, and the Arrow schema definitions used across storage and query.
- **Responsibilities**: Own all shared type definitions (`UniversalRecord`, `EntityKey`, `TimestampConfidence`, `ArtifactSource`); own Arrow `SchemaRef` construction for each artifact category; own schema versioning and migration metadata.
- **Inputs**: None (leaf dependency).
- **Outputs**: Rust types and Arrow schemas consumed by all other crates.
- **Dependencies**: `arrow-rs` only.
- **Performance Target**: Zero-cost — this crate contains only type definitions and schema constants, no runtime logic beyond schema construction (executed once at process start, cached in a `OnceLock`).
- **Error Cases**: Schema version mismatch between a Parquet file's embedded schema and the running binary's expected schema triggers a `SchemaMigrationRequired` error (Section 36.3), never a silent reinterpretation.
- **Future Extension**: Additional artifact categories are added as new schema modules without modifying existing ones (open/closed principle enforced via a schema registry, Section 32.4).

### 11.2 `cfe-orchestrator`
- **Purpose**: Wire the pipeline stages together for a given job (ingest, query, report) and manage concurrency and progress reporting.
- **Responsibilities**: Build a directed acyclic task graph per job; dispatch tasks to the scheduler (Section 34); aggregate progress/error events; expose a job-status stream to CLI/GUI.
- **Inputs**: Job specification (evidence paths, config, requested operations).
- **Outputs**: Job result (success/partial/failure), structured logs, a job manifest.
- **Dependencies**: `cfe-schema`, `cfe-acquire`, `cfe-parsers`, `cfe-storage`, `cfe-query`.
- **Performance Target**: Orchestration overhead < 1% of total job wall-clock time.
- **Error Cases**: Partial ingestion failure (one artifact source fails) does not abort the whole job; failures are collected and reported per-source, and the job manifest marks affected sources explicitly.
- **Future Extension**: Distributed job execution across multiple machines (Section 39, Milestone 4).

## 12. Evidence Engine

### 12.1 Acquisition Layer
- **Purpose**: Provide a uniform, streaming read interface over heterogeneous evidence containers.
- **Responsibilities**: Detect container format by magic bytes (never by file extension); expose a `SeekRead` streaming trait over raw dd, EWF/E01 (via a pure-Rust EWF reader), AFF4, and plain-file/directory sources; enforce read-only access at the OS level (open with read-only flags; refuse to proceed if write access is detected as possible on a block device).
- **Inputs**: Path or block-device handle to the evidence container.
- **Outputs**: A `EvidenceSource` trait object providing chunked, seekable, read-only byte access plus container metadata (size, sector size, embedded hash if present in E01).
- **Internal Workflow**: (1) sniff magic bytes, (2) construct the matching container reader, (3) validate internal container checksums (E01 CRC/MD5 sections) before trusting any embedded metadata, (4) register the source in the chain-of-custody log with an acquisition timestamp and examiner identity from config.
- **Dependencies**: `cfe-schema` for metadata types.
- **Performance Target**: Container overhead (format detection + validation) < 200ms regardless of container size, since validation of embedded checksums is itself streamed rather than requiring a full pre-read.
- **Error Cases**: Corrupt container header → `AcquisitionError::InvalidContainer` with byte offset; checksum mismatch → `AcquisitionError::IntegrityFailure`, job halts for that source (integrity failures are never downgraded to warnings).
- **Future Extension**: Network-acquired evidence (F-Response, remote agent) as an additional `EvidenceSource` implementation (Roadmap Milestone 4).

### 12.2 Hash Verification
- **Purpose**: Establish and continuously verify the cryptographic identity of evidence.
- **Algorithm choice**: SHA-256 is mandatory and computed as evidence streams through acquisition (single pass, no re-read). MD5 and SHA-1 are computed in the same pass only if legacy CASE/E01 compatibility is requested in config, to avoid a second traversal by default.
- **Complexity**: O(n) time in evidence size, O(1) additional memory (streaming hash state, 32–64 bytes).
- **Why chosen**: SHA-256 has no known practical collision attacks and is the de facto standard accepted by courts and CASE/UCO tooling; computing legacy hashes only on demand avoids doubling I/O cost for the common case.
- **Verification**: On any subsequent access to a previously-acquired evidence source (re-opening a case), CFE recomputes the hash and compares against the stored manifest value; mismatch raises `IntegrityFailure` and blocks further operations on that source until acknowledged by the examiner via an explicit override flag, which is itself logged.

### 12.3 Chain of Custody
- **Purpose**: Provide a tamper-evident, append-only log of every operation performed against evidence.
- **Data structure**: An append-only log of custody events, each containing: operation type, timestamp (UTC, from monotonic clock + wall clock cross-check), operator identity (from config/environment), input hash(es), output hash(es) where applicable, and a hash of the previous log entry (hash chain, similar in spirit to a blockchain but without consensus — purely tamper-evidence, not distributed consensus).
- **Why a hash chain**: Any modification to a historical entry breaks the chain from that point forward, and this is detectable in O(n) time with a single verification pass — sufficient for single-examiner or single-lab tamper evidence without the complexity of a distributed ledger.
- **Storage**: Append-only JSON Lines file, one entry per line, co-located with the case directory; the file itself is hashed and the hash recorded in the case manifest.
- **Error Cases**: Any attempt to open the custody log in write+truncate mode is refused at the API level (the log type only exposes an `append` method, never `write` or `truncate`).

### 12.4 Immutable Evidence
- **Purpose**: Guarantee that once ingested, both raw evidence references and derived columnar data cannot be silently altered.
- **Mechanism**: Raw evidence is never copied or modified — CFE reads it read-only and stores only hashes and derived data. Derived Parquet stores are written with content-addressed file names (hash of the row group's serialized bytes) and marked read-only at the filesystem level after the writer closes the file. Corrections (e.g., a parser bug is fixed and artifacts are re-parsed) produce a new, separate revision directory linked to the previous one via a `derived_from` pointer in the case manifest; the old revision is retained, never deleted, unless the examiner explicitly runs a retention-policy purge (logged as a custody event).

## 13. Storage Layer

### 13.1 Columnar Engine (Why Columnar)
Forensic queries overwhelmingly filter and aggregate on a small subset of columns (timestamp, path, process name, hash) across huge row counts. Row-oriented storage forces reading entire records to test one predicate; columnar storage reads only the columns referenced by a query, and — critically — permits SIMD-vectorized predicate evaluation over contiguous, typed memory (Section 14). This is the foundational architectural choice of the entire project and is non-negotiable for v1 and beyond.

**Alternatives considered and rejected**:
- *Row-oriented embedded DB (SQLite-style)*: simpler, but predicate evaluation cost scales with full-row width regardless of which columns are filtered; rejected for failing the core performance goal.
- *Custom binary row format*: avoids a dependency but reinvents encoding/compression/SIMD kernels that Arrow already provides at production quality; rejected on maintainability grounds.
- *Arrow + Parquet (chosen)*: mature, widely adopted, SIMD-friendly in-memory format (Arrow) with a proven compressed on-disk columnar format (Parquet) and first-class Rust support (`arrow-rs`, `parquet` crates maintained by the Apache Arrow Rust project).

### 13.2 Apache Arrow Integration
Arrow `RecordBatch` is the exclusive in-memory representation for all parsed and query-intermediate data. Every parser emits `RecordBatch` values directly (via `ArrayBuilder`s matching the Universal Artifact Schema, Section 32) rather than emitting intermediate Rust structs that are later converted — this avoids an extra allocation/copy pass and is enforced by the `Parser` trait signature (Section 16.2) returning `RecordBatch` directly.

### 13.3 Apache Parquet Integration
On-disk persistence uses Parquet with the following mandatory settings, chosen as the fixed standard (no per-deployment variance, for reproducibility):
- Row group size: 128 MB target (uncompressed), balancing row-group pruning granularity against per-row-group metadata overhead.
- Compression: Zstd level 9 for archival partitions, Zstd level 3 for hot/recently-ingested partitions (configurable per case retention policy, Section 25).
- Dictionary encoding: enabled for all string and low-cardinality columns (Section 13.4).
- Statistics: min/max/null-count column statistics enabled on every column, used by the query planner for row-group pruning before bitmap indexes are even consulted (cheaper first-pass filter).

### 13.4 Dictionary Encoding & Bitmap Index
- **Dictionary encoding**: Every column with estimated cardinality below 100,000 distinct values within a row group (paths, process names, registry key names, artifact source identifiers) is dictionary-encoded: values are stored once in a dictionary array and referenced by integer index in the data array. This reduces both storage size and comparison cost (integer equality instead of string comparison) and is a prerequisite for bitmap indexing.
- **Bitmap index**: For each dictionary-encoded column marked indexable (a fixed, documented list per artifact schema — Section 32.3), CFE builds a Roaring Bitmap per distinct dictionary value, mapping value → set of row positions containing it. Roaring Bitmaps are chosen over plain bitsets because they compress sparse and dense regions adaptively (run-length for dense ranges, array/bitset containers for sparse ranges), giving both fast set operations (AND/OR/NOT in effectively O(n/64) with SIMD-friendly word operations) and compact storage for the low-to-medium cardinality columns typical of forensic data.
- **Alternatives rejected**: B-tree secondary indexes (higher point-lookup cost for the multi-value AND/OR patterns forensic queries require); inverted list without compression (excessive memory at billion-row scale).
- **Memory layout**: dictionary arrays are stored as `Utf8Array` (offsets + values buffers, contiguous), index arrays as `Int32Array`, bitmap indexes as serialized Roaring Bitmap byte buffers memory-mapped directly from the `.cfeidx` sidecar file (Section 35.3) — no deserialization copy on load.

### 13.5 Compression
Default Zstd is chosen over Snappy/LZ4/Gzip because it offers materially better compression ratio at comparable-or-better decompression speed for the text-heavy, repetitive nature of forensic artifact data (paths, registry values, event log message templates), and `zstd-rs` provides a mature, safe Rust binding. Compression level is a per-partition-tier setting (Section 13.3), not a global constant, because ingest-time (hot) partitions favor lower latency while archival partitions favor size.

## 14. SIMD Engine & Parallel Engine

### 14.1 SIMD Engine
- **Purpose**: Evaluate predicates and aggregations over Arrow arrays using vectorized CPU instructions rather than scalar per-row loops.
- **Approach**: CFE uses `arrow-rs`'s `arrow::compute` kernels (`eq`, `gt`, `lt`, `like`, `and_kleene`, `or_kleene`, sum/min/max aggregations) as the baseline, all of which are auto-vectorized or explicitly SIMD-implemented upstream for `x86_64` (AVX2, with AVX-512 where the runtime CPU supports it, detected via `std::is_x86_feature_detected!`) and `aarch64` (NEON). CFE does not hand-roll SIMD intrinsics for standard predicate kernels — this avoids duplicating a well-tested upstream implementation. Custom kernels (Section 14.3) are only written for forensic-specific operations not covered by `arrow::compute` (e.g., path-glob matching, MAC address prefix matching).
- **Why not hand-rolled intrinsics everywhere**: `arrow-rs` kernels are maintained, fuzzed, and benchmarked by the broader Arrow community; reimplementing them would add maintenance burden without a measurable performance benefit for standard predicate types. Hand-rolled kernels are reserved for genuinely forensic-specific patterns.
- **Complexity**: SIMD-evaluated predicates over a batch of length n run in O(n/w) vector operations where w is the SIMD lane width (8 for AVX2 32-bit lanes, 16 for AVX-512), versus O(n) scalar operations — a 4–16x constant-factor throughput improvement depending on hardware and data type.

### 14.2 Parallel Engine
- **Purpose**: Distribute query execution and ingestion across CPU cores.
- **Approach**: `rayon` work-stealing thread pool is the standard parallelism primitive for all data-parallel operations (per-row-group predicate evaluation, per-partition ingestion). Row groups and partitions are the unit of parallel work because they are also the unit of pruning (Section 15.3) — a row group either survives pruning and is processed on some worker thread, or is skipped entirely.
- **Why rayon over manual thread management or async**: Ingestion and query execution are CPU-bound, data-parallel workloads with well-defined, statically-known work units (row groups) — exactly rayon's design target. Manual thread pools would reimplement work-stealing; async (tokio) is reserved for I/O-bound orchestration (Section 34.2), not CPU-bound batch processing, to avoid mixing concurrency models within the hot path.

### 14.3 Custom Forensic Kernels
Documented custom kernels: path-glob/regex match (compiled once per query, applied per batch via a byte-level Aho-Corasick or regex-automata scan), MAC-address CIDR-style prefix match, timestamp range match with timezone-aware normalization. Each is benchmarked against a scalar reference implementation in the benchmark suite (Section 26.3) to justify its existence.

## 15. Query Planner & Optimizer

### 15.1 Query Surface
Queries are expressed via a `QueryBuilder` Rust API (the canonical form) and a textual DSL used by the CLI (Section 23) that compiles to the same `QueryBuilder` calls — the DSL is not a separate execution path, only a separate syntax. A query is composed of: artifact-type selection, column projection list, a predicate tree (AND/OR/NOT of column comparisons, ranges, and set-membership tests), an optional entity-correlation join, and an optional sort/limit.

### 15.2 Query Planner
The planner converts a `QueryBuilder` specification into a logical plan tree (Scan → Filter → Project → Join → Sort), then a physical plan bound to specific row groups and index files, following classic relational query-planning structure adapted to a columnar, index-augmented, single-node execution model.

### 15.3 Query Optimizer
Optimization passes, applied in fixed order (deterministic — order never varies by data, only by config):
1. **Predicate pushdown**: filters are pushed to the earliest possible stage — Parquet row-group statistics pruning first (cheapest), then bitmap index lookups (cheap, no row materialization), then SIMD scalar evaluation only for predicates that cannot be answered by statistics or bitmaps (e.g., substring match).
2. **Projection pushdown**: only columns referenced in the predicate tree, join keys, or output projection are ever decoded from Parquet; all other columns remain unread.
3. **Row-group pruning**: row groups whose min/max statistics cannot satisfy the predicate are skipped without opening the row group's data pages at all.
4. **Bitmap intersection ordering**: when multiple bitmap-indexed predicates are ANDed, the optimizer evaluates the most selective (smallest resulting bitmap, estimated from stored cardinality metadata) predicate first, intersecting progressively to minimize intermediate set sizes.

### 15.4 Predicate & Projection Pushdown — Why This Order
Statistics-based row-group pruning is checked first because it requires no I/O beyond already-loaded Parquet footer metadata. Bitmap index lookups are checked second because they require reading only the small `.cfeidx` sidecar, not the data column itself. Full SIMD scalar evaluation is the last resort because it requires decoding the actual column data. This ordering minimizes I/O and decode work monotonically at each stage.

## 16. Streaming Parser Framework

### 16.1 Purpose
Provide a uniform, bounded-memory, panic-safe execution contract that every artifact-specific parser (Section 17) implements, so the orchestrator and storage layer never need artifact-specific knowledge.

### 16.2 Parser Trait Contract
Every parser implements a `Parser` trait with: (a) a `sniff(&[u8]) -> Confidence` function used for format detection independent of file extension, (b) a `parse_streaming(reader: impl Read + Seek, sink: &mut RecordBatchSink) -> Result<ParseReport, ParseError>` function that emits `RecordBatch` values into a bounded-capacity sink as it goes (never buffering the whole artifact in memory), and (c) declared metadata: supported format versions, known limitations, and validation rules (per the mandatory parser template, Section 17.1).

### 16.3 Internal Workflow
A parser reads fixed-size windows of the source (window size is format-dependent, e.g., 1024 bytes per MFT record, 1 event per Evtx chunk), validates structural invariants (magic numbers, checksums where the format defines them, record length bounds), converts valid records into `UniversalRecord` fields immediately, and reports invalid/truncated records as structured `ParseError` entries attached to the job's error log rather than aborting — a single corrupt record must never stop parsing of the remaining, valid records.

### 16.4 Error Handling Contract
Parsers must never panic on malformed input; all fallible operations (integer casts, string decoding, bounds-checked slicing) return `Result`/`Option` and are propagated as a per-record `ParseError` with the byte offset of the failure. This is enforced in CI via fuzz testing (Section 27.3) against every parser using corpora of truncated/mutated real artifact samples.

## 17. Artifact Parsers

### 17.1 Mandatory Parser Documentation Template
Every parser listed below documents: supported versions, limitations, validation approach, known edge cases, and performance expectation, per project-wide requirement.

### 17.2 Filesystem Parsers

**MFT ($MFT, NTFS)**
- Supported versions: NTFS 3.0–3.1 (Windows XP through Windows 11) MFT record layout, record size 1024 bytes (with 4096-byte record size support behind a config flag for very large volumes).
- Limitations: does not reconstruct deleted-and-overwritten record fragments; resident vs non-resident attribute handling covers `$STANDARD_INFORMATION`, `$FILE_NAME`, `$DATA` (non-resident runlist parsed for fragmentation/allocation metadata, not for full file-content carving — that is out of scope, Section 5).
- Validation: record signature `FILE0`/`BAAD` checked per record; fixup array validated and applied before trusting sector data.
- Known edge cases: records marked `BAAD` (fixup mismatch) are reported as corrupted but still yield partial `$STANDARD_INFORMATION` if the fixup-affected sectors don't overlap it.
- Performance expectation: ≥ 200,000 MFT records/second single-threaded on reference hardware (Section 28).

**USN Journal ($UsnJrnl:$J)**
- Supported versions: USN_RECORD_V2 (standard) and USN_RECORD_V3 (64-bit file references, Windows 8+).
- Limitations: journal wrap (older records overwritten) is detected via sequence-number gaps and reported in the parse report, not silently ignored.
- Validation: record length field cross-checked against buffer bounds before advancing the read cursor.
- Known edge cases: sparse journal files with large zero-filled gaps are skipped efficiently via seek rather than reading zeros.
- Performance expectation: ≥ 1,000,000 records/second single-threaded.

### 17.3 Windows Artifacts

**Windows Event Log (.evtx)**
- Supported versions: Evtx binary XML format (Windows Vista+).
- Limitations: does not evaluate/execute embedded XPath in Event Log providers' message resource DLLs for message template rendering by default (optional, off-by-default feature requiring explicit resource-DLL paths, since resolving templates from arbitrary DLLs is an untrusted-code-execution risk — see Section 29).
- Validation: chunk checksum (CRC32) validated per 64KB chunk before parsing contained records; per-record CRC also validated.
- Known edge cases: dirty/incompletely-flushed files (chunk marked dirty flag) are parsed with best-effort recovery, flagged in the parse report.
- Performance expectation: ≥ 100,000 events/second single-threaded.

**Registry (NTUSER.DAT, SYSTEM, SOFTWARE, etc.)**
- Supported versions: Registry hive format versions 1.3–1.6.
- Limitations: does not attempt reconstruction of unallocated ("free") cell data by default (optional deep-scan mode enables free-cell carving, off by default due to higher false-positive rate — an explicit, documented tradeoff).
- Validation: hive base block checksum (XOR-32) validated; cell size/signature checked before dereferencing offsets.
- Known edge cases: transaction log (.LOG/.LOG1/.LOG2) replay is supported as a documented optional pre-processing step, not automatic, because it mutates the effective hive state being analyzed and must be explicit for reproducibility.
- Performance expectation: ≥ 50,000 keys+values/second single-threaded.

**Prefetch (.pf)**
- Supported versions: format versions 17 (XP/2003) through 30/31 (Windows 10/11), including MAM-compressed (Windows 8+) via a documented decompression step.
- Validation: signature bytes and decompressed-size field checked before decompression buffer allocation (prevents unbounded allocation from a malicious size field — bounded to a configurable max, default 64MB).
- Known edge cases: version 30/31 files with embedded volume information beyond the documented count are truncated gracefully with a warning.
- Performance expectation: ≥ 5,000 files/second single-threaded.

**Amcache.hve / Shimcache (AppCompatCache)**
- Supported versions: Amcache hive schema as seen in Windows 8.1–11 (schema drift across builds handled via a versioned field-mapping table, not conditional spaghetti code); Shimcache formats for Windows 7/8/10/11 registry-embedded binary structures.
- Limitations: Shimcache entry timestamps are execution-adjacent, not confirmed-execution, evidence — this distinction is preserved via the `TimestampConfidence` field (Section 32.2), never conflated with confirmed execution.
- Validation: signature/magic bytes per entry; entry count cross-checked against declared buffer length.
- Performance expectation: ≥ 20,000 entries/second single-threaded.

**Jump Lists / LNK**
- Supported versions: Shell Link Binary File Format as used by .lnk files and embedded within AutomaticDestinations/CustomDestinations Jump List OLE-compound-file streams.
- Limitations: OLE compound file parsing covers the subset of structures needed to extract embedded LNK streams; general-purpose OLE document parsing (e.g., legacy Office documents) is out of scope.
- Validation: LNK header GUID and header size validated before parsing optional structures (LinkInfo, StringData).
- Known edge cases: LNK files with corrupted optional `ExtraData` blocks yield the base LinkInfo/target path with the corrupted block reported and skipped, not a total parse failure.
- Performance expectation: ≥ 2,000 files/second single-threaded.

### 17.4 Linux Artifacts
Supported: `utmp`/`wtmp`/`btmp` login records, syslog/journald (binary journal format), bash/zsh history files, cron tables, `/etc/passwd`+`/etc/shadow` metadata (no password cracking — hash values are stored as opaque strings only), package manager logs (dpkg/rpm). Each follows the same mandatory documentation template as Section 17.2–17.3 artifacts; full per-artifact tables are maintained in `docs/parsers/linux/` and are schema-versioned identically to Windows artifacts.

### 17.5 macOS Artifacts
Supported: Unified Logging (`.tracev3`) via the documented Apple Unified Log binary format, `plist` (binary and XML) parsing for LaunchAgents/LaunchDaemons and application preference files, FSEvents stream files, Spotlight metadata store (`.store.db`) index parsing. Same documentation template applies; details maintained in `docs/parsers/macos/`.

### 17.6 Browser Artifacts
Supported: Chromium-family (SQLite-based History, Cookies, Web Data — parsed via CFE's own read-only SQLite page parser, not by linking `libsqlite3`, to keep the dependency footprint and trust boundary minimal per Section 29) and Firefox (`places.sqlite`, same approach). Bookmarks, downloads, form history, and autofill are all mapped into the Universal Artifact Schema's browser-activity record type. Details maintained in `docs/parsers/browser/`.

## 18. Timeline Engine

### 18.1 Purpose & Algorithm
Produce a single, globally time-ordered event stream across every ingested artifact type for a case.

### 18.2 Timestamp Normalization & Confidence
Every source timestamp is converted to UTC nanosecond epoch at parse time (Section 10.3), tagged with a `TimestampConfidence` enum: `Confirmed` (e.g., a signed, checksum-validated event log record timestamp), `Derived` (e.g., Shimcache execution-adjacent time), or `Estimated` (e.g., a timestamp reconstructed from surrounding context). The timeline never silently treats a `Derived`/`Estimated` timestamp as equally reliable as `Confirmed` — confidence is a first-class, always-displayed column, never metadata that can be dropped.

### 18.3 Timeline Algorithm
Because each partition's Parquet row groups are already stored sorted by ingestion-local timestamp per artifact type (a storage-layer invariant enforced at write time, Section 13.2), producing a global timeline is a k-way merge across per-partition sorted streams rather than a full sort of all rows.
- **Complexity**: O(n log k) where n is total row count and k is the number of partitions being merged, versus O(n log n) for a naive full sort — a meaningful improvement when k (partition count) is much smaller than n (row count), which is the typical forensic case.
- **Memory**: O(k) for the merge heap plus O(batch size) per partition read buffer — never O(n).
- **Alternative rejected**: external merge sort over unsorted input — rejected because it discards the sorted-partition invariant CFE already maintains at no extra cost during ingestion (Section 13.2 requires row groups to be flushed in timestamp order per partition).

## 19. Correlation Engine

### 19.1 Purpose & Algorithm
Join events across different artifact types that share an investigative entity (a process instance, a user session, a USB device, a file by hash) even though each artifact type stores that entity under different column names and sometimes different representations.

### 19.2 Entity Key Model
The Universal Artifact Schema (Section 32.2) declares a fixed set of `EntityKey` variants (ProcessKey{pid, start_time_bucket}, UserSessionKey{sid, logon_id}, DeviceKey{serial}, FileKey{sha256}, NetworkKey{mac}). Every parser that can populate an entity key for its artifact type does so at parse time; the correlation engine never guesses keys from unstructured text.

### 19.3 Correlation Algorithm
Correlation is implemented as a hash join: build a hash table keyed by `EntityKey` over the smaller of two artifact partitions (chosen via stored row-count metadata, avoiding a full scan to decide build vs. probe side), then probe with the larger partition, streaming probe-side row groups through the query engine's normal batch pipeline (Section 15) so correlation reuses the same pruning/pushdown machinery rather than being a separate, unoptimized code path.
- **Complexity**: O(n + m) expected time for a hash join of n and m rows, versus O(n·m) for a naive nested-loop join; O(min(n,m)) memory for the build-side hash table.
- **Alternative rejected**: sort-merge join — rejected as the default because it requires sorting both sides by the join key (which is not the natural on-disk sort order — that's timestamp, Section 18.3), whereas hash join avoids a resort; sort-merge remains available as a planner fallback (Section 15.3) when the build side would not fit the configured memory budget.

## 20. Rule Engine

### 20.1 Rule Definition
Rules are declarative TOML documents specifying: an artifact-type scope, a predicate tree (identical grammar to Section 15.1 query predicates — rules are not a separate language), an optional correlation pattern (a small graph of entity-key joins with per-edge time-window constraints), and a severity/description for reporting. No rule may embed arbitrary code, scripting, or external process invocation — this is enforced by the TOML schema itself (no field accepts a code string) to preserve the "no unexplainable heuristic" principle (Section 5).

### 20.2 Rule Evaluation
Rule predicates compile to the same logical/physical query plan machinery as ad hoc queries (Section 15), and correlation patterns compile to the same hash-join engine (Section 19.3). This guarantees rule evaluation performance and correctness track the query engine's, with no separately-maintained execution path to drift out of sync.

## 21. Report Engine
- **Purpose**: Render query results, timelines, correlation results, and rule hits into the export formats required by FR-10 (Section 6), always including a chain-of-custody appendix (Section 12.3) sourced directly from the case's custody log, never re-derived or summarized lossily.
- **Responsibilities**: Format-specific renderers (Section 38) consume a common `ReportModel` intermediate representation so adding a new export format never requires touching query/timeline/correlation code.
- **Performance Target**: Report rendering for a 100,000-row finding set completes in under 5 seconds for HTML/JSON/CSV, and under 30 seconds for PDF (PDF rendering is the slowest due to layout/pagination, Section 38.5).

## 22. Plugin Architecture
- **Purpose**: Allow new artifact parsers and new rule predicate extensions to be added without modifying `cfe-parsers` or `cfe-rules` core code.
- **Mechanism**: Plugins are compiled Rust crates implementing the `Parser` trait (Section 16.2) or a `RulePredicateExtension` trait, registered via a build-time plugin registry (a `linkme`-based distributed slice, not dynamic `dlopen` loading — dynamic loading of untrusted `.so`/`.dll` files is explicitly rejected as a supply-chain and sandboxing risk, Section 29.2). Out-of-tree plugins are supported by depending on `cfe-schema` and `cfe-parsers` as library crates and registering at compile time in a downstream binary.
- **Why not dynamic loading**: `dlopen`-based plugins would require CFE to trust arbitrary native code at runtime with no sandboxing boundary, directly conflicting with the "evidence bytes and extensions are both untrusted until proven otherwise" security posture (Section 29).

## 23. CLI
The `cfe` CLI is the primary interface for ingestion, querying, and reporting, structured as subcommands: `cfe acquire`, `cfe ingest`, `cfe query`, `cfe timeline`, `cfe correlate`, `cfe rules run`, `cfe report`, `cfe verify` (chain-of-custody/hash verification). The query DSL accepted by `cfe query` compiles directly to `QueryBuilder` (Section 15.1); the CLI itself contains no independent query logic. All CLI output defaults to human-readable tables; `--format json` switches every subcommand to machine-readable JSON Lines for scripting.

## 24. GUI
The GUI is a separate, optional crate (`cfe-gui`) consuming only the stable `cfe-api` library surface (Section 31) — it has no access to internal crate APIs, enforcing the same contract boundary external integrators would have. The GUI provides case management, visual timeline browsing (virtualized rendering for million-row timelines, never loading the full result set into UI widgets at once), query building, and report preview. GUI implementation detail (framework choice, widget layout) is intentionally left to a dedicated `cfe-gui` design document rather than this PRD, since the GUI is a presentation-layer consumer and does not affect core engine correctness or performance guarantees.

## 25. Configuration
Configuration is a single TOML file per case (`case.toml`) plus an optional global defaults file (`~/.config/cfe/config.toml`), merged with case-level settings taking precedence. Configuration covers: examiner identity (for chain-of-custody), storage tier/compression policy (Section 13.3), parser feature flags (e.g., Evtx message-template resolution, registry deep-scan — both off by default per Sections 17.3/29), resource limits (max memory, thread pool size), and output paths. Configuration is validated eagerly at job start (fail fast, never partway through ingestion) and the resolved, effective configuration is written into the job manifest for reproducibility (Section 3).

## 26. Logging, Metrics & Benchmarking

### 26.1 Logging
Structured logging via the `tracing` crate, emitting JSON Lines to a per-job log file with a monotonic operation ID, timestamp, span (which pipeline stage), and severity. No `println!`/ad hoc logging is permitted in library crates (enforced by a Clippy lint configuration, Section 42.3); only `cfe-cli`'s presentation layer may write directly to stdout for human-facing output.

### 26.2 Metrics
Each pipeline stage records throughput (rows/sec, bytes/sec), memory high-water mark, and per-stage wall-clock duration into the job manifest, enabling post-hoc performance analysis without requiring an external metrics backend for basic use; an optional Prometheus exporter is provided for continuous/server deployments (Section 39, Milestone 3).

### 26.3 Benchmark Suite
`cfe-bench` (a workspace member, Section 43) uses `criterion` for micro-benchmarks (individual parser throughput, SIMD kernel throughput vs. scalar baseline, bitmap index build/query time) and a set of reference-scale end-to-end benchmarks (synthetic 1GB/10GB/100GB evidence corpora) run in CI on every release candidate, with results compared against the previous release to catch regressions (a >5% regression on any tracked benchmark fails the release CI gate, Section 45).

## 27. Testing & Validation Strategy

### 27.1 Unit Testing
Every parser, storage function, and query kernel has unit tests covering the documented known edge cases (Section 17) using minimized, hand-crafted binary fixtures checked into `tests/fixtures/`.

### 27.2 Integration Testing
End-to-end tests run the full pipeline (acquire → parse → store → query → report) against real, licensable/public-domain reference forensic image sets (e.g., publicly available NIST CFReDS images) and assert both row counts and specific known-value spot checks.

### 27.3 Fuzz Testing
Every parser is fuzzed via `cargo-fuzz` (libFuzzer) using corpora seeded from real artifact samples with structure-aware mutation; CI runs a fixed fuzz time budget (default 5 minutes per parser) on every pull request touching that parser, and a longer nightly budget (1 hour per parser) — required per Section 16.4's no-panic contract.

### 27.4 Determinism Validation
A dedicated CI job runs `cfe ingest` twice against the same fixture evidence and asserts byte-identical SHA-256 hashes of all output Parquet/index files, directly validating the determinism requirement (Section 7).

### 27.5 Validation Strategy for Correctness
Parser output for supported artifact types is cross-validated against at least one independent, established reference implementation's output (where one exists and is licensable for test use) on shared fixtures, with discrepancies triaged and either fixed or documented as an intentional, justified deviation (recorded as an ADR, Section 41).

## 28. Performance Targets

Reference hardware baseline: 16-core / 32-thread x86_64 CPU with AVX2, 64GB RAM, NVMe SSD capable of ≥ 3GB/s sequential read.

| Operation | Target |
|---|---|
| Raw evidence hashing | ≥ 1.5 GB/s single stream (SHA-256, hardware-accelerated via `sha2` crate's SIMD backend) |
| MFT parsing | ≥ 200,000 records/sec/core |
| Evtx parsing | ≥ 100,000 events/sec/core |
| Parquet ingest write (with dictionary + bitmap index build) | ≥ 500 MB/s aggregate across 16 cores |
| Indexed point query (bitmap-index hit) | < 50 ms at 1 billion row scale |
| Full predicate scan (no usable index) | ≥ 2 GB/s per core of column data scanned (SIMD kernel throughput) |
| Timeline merge (k=50 partitions, 1B total rows) | < 60 seconds |
| Correlation hash join (100M x 10M rows) | < 30 seconds |
| HTML report render (100K findings) | < 5 seconds |
| PDF report render (100K findings) | < 30 seconds |

All targets are re-validated by the benchmark suite (Section 26.3) on every release; a documented regression exception requires an ADR (Section 41) explaining the tradeoff accepted.

## 29. Security & Threat Model

### 29.1 Threat Model
Primary threat: evidence content is attacker-controlled (a suspect's disk image may contain deliberately malformed artifacts designed to exploit forensic tooling — a well-documented real-world attack class). Therefore every byte read from evidence is treated as untrusted input at every layer, including within already-parsed intermediate representations passed between pipeline stages.

### 29.2 Mitigations
- All parsers are written using safe Rust with bounds-checked slicing; any `unsafe` block (permitted only in narrowly-scoped, documented cases such as SIMD intrinsics or zero-copy memory-mapped reads, Section 42.2) is isolated, unit-tested, and fuzz-tested independently of surrounding safe code.
- No parser executes, links against, or interprets evidence content as code (e.g., no invoking `libsqlite3`'s SQL engine on attacker-controlled database files without CFE's own read-only page-level parser as a safer alternative, Section 17.6; no default resolution of Evtx message-template resource DLLs, Section 17.3).
- All decompression routines (Prefetch MAM, Parquet Zstd) enforce a configurable maximum output size before allocating, preventing decompression-bomb-style memory exhaustion.
- Dynamic plugin loading is rejected by design (Section 22) to avoid an unsandboxed native-code trust boundary.
- Dependency policy (Section 44) restricts what unsafe/native-code dependencies may be introduced at all.

### 29.3 Non-Threats (Explicitly Out of Scope)
Protecting against a malicious *operator* (an examiner intentionally falsifying findings) is out of scope for the engine itself — the chain-of-custody hash chain (Section 12.3) provides tamper *evidence*, not tamper *prevention*, which is an accepted, documented tradeoff (see ADR-0007, Section 41).

## 30. Error Handling
All fallible operations return `Result<T, CfeError>` where `CfeError` is a structured, per-crate error enum (e.g., `AcquisitionError`, `ParseError`, `StorageError`, `QueryError`) implementing `std::error::Error` with source-chaining via `thiserror`, never `anyhow`-style opaque errors in library crates (opaque errors are acceptable only in `cfe-cli`'s top-level binary for terminal presentation). Panics are treated as bugs; `#![deny(clippy::unwrap_used, clippy::expect_used)]` is enforced workspace-wide outside of test code (Section 42.3). Every error variant that can occur mid-ingestion is designed to be recoverable at the record level (Section 16.3) rather than aborting the whole job, except for integrity failures (Section 12.2) and configuration validation failures (Section 25), which are correctness-critical and must halt.

## 31. API Design
`cfe-api` is the single stable, semantically-versioned library surface for all external consumers (CLI, GUI, third-party automation). Every public function is documented with: request shape, response shape, error variants, the API version it was introduced in, and its compatibility guarantee (per the mandatory API documentation requirement). Example:

```rust
/// Introduced: v1.0. Compatibility: additive-only (new optional fields via
/// #[non_exhaustive] structs); no field removal without a major version bump.
pub fn query(
    case: &CaseHandle,
    request: QueryRequest,
) -> Result<QueryResponse, CfeApiError>;
```
`QueryRequest`/`QueryResponse` are `#[non_exhaustive]` structs so that adding optional fields in minor versions cannot break downstream pattern matches. Breaking changes (field removal, semantic change to existing behavior) require a major version bump and a migration note in `CHANGELOG.md`, per the backward-compatibility non-functional requirement (Section 7).

## 32. Internal Data Format & Universal Artifact Schema

### 32.1 Purpose
The Universal Artifact Schema (UAS) is the sole normalized representation every artifact type is mapped into, and the only contract between the parsing plane and every downstream plane (Section 8).

### 32.2 Core Fields (present on every UAS record)
`record_id` (case-unique, deterministic hash of source file hash + source offset — not a random UUID, to preserve determinism), `artifact_type` (dictionary-encoded enum), `source_hash` (SHA-256 of the originating file), `timestamp_utc_ns` (i64, nullable if the artifact type has no inherent timestamp), `timestamp_confidence` (`Confirmed`/`Derived`/`Estimated`, Section 18.2), `entity_keys` (a small fixed-size list of `EntityKey` variants populated where applicable, Section 19.2), and an `extra` map-typed column (Arrow `MapArray`) preserving any source fields not promoted to a first-class typed column, guaranteeing FR-3's no-data-loss requirement.

### 32.3 Per-Artifact-Type Extensions
Each artifact type (Section 17) additionally defines its own first-class typed columns (e.g., MFT records have `file_name`, `parent_record_number`, `is_directory`; Evtx records have `event_id`, `provider_name`, `channel`) via a schema-registry entry (Section 32.4) declaring the Arrow field list, which of those fields are indexable (Section 13.4), and the schema version.

### 32.4 Schema Registry & Versioning
A compile-time registry (analogous in spirit to the plugin registry, Section 22) maps `artifact_type` to its current Arrow schema and a migration function from each prior schema version, so that Parquet files written by an older CFE version remain readable (Section 7's backward compatibility requirement) — the reader always upgrades an older record batch to the current in-memory schema via the registered migration function before it reaches the query engine, never via ad hoc per-call-site version checks scattered through the codebase.

## 33. Memory Management

### 33.1 Buffer Pool
Ingestion and query execution draw batch-sized buffers from a fixed-capacity pool (sized from the configured memory budget, Section 25) rather than allocating fresh per batch; buffers are returned to the pool on batch completion. This bounds peak memory deterministically (Section 7's memory non-functional requirement) and avoids allocator pressure/fragmentation under sustained high-throughput ingestion.

### 33.2 Cache Strategy
Bitmap index sidecar files (Section 13.4) and Parquet footer metadata are cached in an LRU cache sized as a configurable fraction of the memory budget, since both are read repeatedly across many queries within a session and are far smaller than the underlying column data — caching them avoids redundant disk I/O for the query planner's row-group-pruning stage (Section 15.4) without risking unbounded memory growth.

## 34. Scheduler & Threading Model

### 34.1 Threading Model
Two distinct thread pools are used, deliberately kept separate: a `rayon` work-stealing pool for CPU-bound, data-parallel batch processing (parsing, predicate evaluation, aggregation — Section 14.2), and a small `tokio` runtime used only by `cfe-orchestrator` for I/O-bound job coordination (progress event streaming to CLI/GUI, file-system watch for job cancellation signals). CPU-bound work is never scheduled onto the tokio runtime and I/O-bound orchestration is never scheduled onto the rayon pool — mixing them would cause tokio's cooperative scheduler to stall behind long-running CPU work, and is explicitly disallowed by a `cfe-orchestrator` internal lint/test that flags any `rayon::spawn` call originating from within a tokio task.

### 34.2 Async Strategy
Async/await is confined to the orchestration boundary described above; no parser, storage, or query kernel code is written as `async fn`, since none of that code is actually I/O-bound in the sense async is designed for (columnar batch processing is CPU-bound; disk reads are handled via `mmap`, Section 35.2, which is inherently synchronous from Rust's point of view and is offloaded to rayon worker threads rather than wrapped in async).

## 35. Disk I/O, Zero-Copy & Memory Mapping

### 35.1 Disk I/O Strategy
Evidence acquisition (Section 12.1) uses buffered sequential reads sized to the container's natural chunk size (sector-aligned) to maximize sequential throughput. Parquet/index reads during query execution use memory mapping (Section 35.2) rather than explicit `read()` calls wherever the underlying OS/filesystem supports it, falling back to buffered reads on filesystems without mmap support (some network filesystems), detected once at case-open time and recorded in the job manifest.

### 35.2 Zero-Copy Strategy & Memory Mapping
Parquet column chunks and `.cfeidx` bitmap index files are opened via `memmap2` and interpreted directly as Arrow buffers/Roaring Bitmap byte layouts without an intermediate deserialization copy — both formats are designed to be read this way (Arrow's spec is explicitly a zero-copy wire/file format; Roaring Bitmaps' portable serialization format is likewise designed for direct interpretation). This avoids a full-column copy on every query, which matters materially at multi-GB column sizes. Safety: mmap'd regions are only ever read, never written, and CFE validates buffer bounds/alignment against the Arrow schema before constructing any typed view over the mapped bytes, guarding against a truncated or corrupt file causing an out-of-bounds read (enforced via `arrow-rs`'s own buffer validation, not bypassed).

### 35.3 Buffer Pool Interaction
Memory-mapped pages are managed by the OS page cache, not CFE's own buffer pool (Section 33.1) — the buffer pool governs CFE-allocated batch buffers (used for in-flight query intermediate results, parser output before it's flushed to Parquet), while mmap'd read-only source data relies on the OS's existing, already-optimized page cache eviction, avoiding duplicated caching logic.

## 36. Storage Format & Index Strategy

### 36.1 Layout
A case directory contains: `case.toml` (config), `custody.log` (chain of custody, Section 12.3), `manifest.json` (evidence sources, hashes, job history), and a `data/` directory partitioned as `data/<artifact_type>/<source_id>/<time_bucket>/*.parquet` with matching `.cfeidx` sidecar files per indexed column per Parquet file.

### 36.2 Versioning & Validation
Every Parquet file embeds the UAS schema version (Section 32.4) in its Parquet key-value metadata; every `.cfeidx` file embeds a small fixed header (magic bytes, format version, source Parquet file hash it was built from, indexed column name) validated before the bitmap data is trusted. A checksum (CRC32C) covers the `.cfeidx` header and is validated on every mmap open, not just at build time — protecting against silent filesystem corruption between ingestion and later query sessions.

### 36.3 Forward/Backward Compatibility
Readers must support at least the two previous major schema versions via the migration-function mechanism (Section 32.4); a schema version older than that requires an explicit `cfe migrate` command to upgrade the on-disk files in place (writing new Parquet files under a new revision directory per the immutable-evidence policy, Section 12.4, never overwriting in place) before they can be queried by the current binary. This bound (two versions) is a deliberate, documented tradeoff between indefinite backward-compatibility maintenance burden and practical upgrade friction for long-lived cases.

## 37. Search Engine
Free-text search (e.g., searching path/registry-value/message-text columns for a substring or regex not covered by a bitmap index) is implemented as a SIMD-accelerated substring/regex scan (Section 14.3) over dictionary values rather than a general-purpose inverted full-text index (e.g., Tantivy-style), because forensic string search predominantly targets a small set of already dictionary-encoded, moderate-cardinality columns — scanning the (deduplicated) dictionary once and then intersecting the resulting value-index bitmap is cheaper than maintaining a separate full-text index structure for this access pattern, and keeps the storage format simpler (one index mechanism, Section 13.4, rather than two).

## 38. Export Formats

### 38.1 CASE/UCO JSON
The primary interoperable format, mapping UAS records (Section 32) and their entity keys/correlations to CASE (Cyber-investigation Analysis Standard Expression) / UCO ontology objects, chosen because it is the emerging community and NIST-recognized standard for cross-tool forensic data exchange, satisfying court-defensibility and cross-tool interoperability goals better than a bespoke JSON schema would.

### 38.2 Flat JSON
A simpler, UAS-shaped JSON Lines export (one record per line) for scripting/automation use cases where full CASE ontology mapping is unnecessary overhead.

### 38.3 CSV
Flattened, single-artifact-type-per-file CSV export (the `extra` map column, Section 32.2, is serialized as a nested JSON string within a single CSV cell, since CSV has no native nested-structure support) for spreadsheet-based review workflows.

### 38.4 HTML Report
A self-contained (no external network resource dependencies, for offline/air-gapped review) HTML report with an embedded, virtualized timeline/table view and the chain-of-custody appendix (Section 21).

### 38.5 PDF Report
Generated from the same `ReportModel` (Section 21) as HTML, via a paginated layout renderer; slower than HTML/JSON/CSV (Section 28) due to pagination/layout computation, and therefore recommended for final, fixed-content deliverables rather than iterative review.

## 39. Roadmap & Milestones

| Milestone | Scope |
|---|---|
| M1 — Core Engine | Acquisition, hashing, chain of custody, Arrow/Parquet storage, dictionary + bitmap indexing, query planner/optimizer, SIMD execution, CLI. Filesystem + core Windows artifact parsers (MFT, USN, Evtx, Registry, Prefetch). |
| M2 — Analytics | Timeline engine, correlation engine, rule engine, remaining Windows/Linux/macOS/browser artifact parsers, HTML/JSON/CSV export. |
| M3 — Enterprise | GUI, PDF/CASE export, Prometheus metrics exporter, plugin SDK for out-of-tree parsers. |
| M4 — Distributed | Remote/network acquisition sources, distributed job execution across multiple machines for very large multi-custodian cases. |

## 40. Risks

| Risk | Mitigation |
|---|---|
| Parser schema drift across OS versions (e.g., Amcache format changes across Windows builds) | Versioned field-mapping tables (Section 17.3) and integration tests against multiple OS-version fixtures. |
| Bitmap index memory growth at extreme cardinality | Cardinality thresholds (Section 13.4) fall back to non-indexed scan for above-threshold columns, with the threshold itself configurable and logged. |
| Determinism regressions introduced by future dependency upgrades (e.g., a HashMap iteration-order change) | CI determinism validation job (Section 27.4) catches this on every PR before merge. |
| Parquet/Arrow upstream breaking changes | Pinned dependency versions (Section 44) with a deliberate, reviewed upgrade process rather than automatic minor/patch tracking. |
| Legal/court-defensibility challenges to novel storage format | CASE/UCO export (Section 38.1) and full chain-of-custody/hash verification (Sections 12.2–12.3) preserve independent verifiability regardless of CFE's internal storage format. |

## 41. Decision Records (ADR Index)

- **ADR-0001**: Chosen columnar (Arrow/Parquet) over row-oriented storage (Section 13.1).
- **ADR-0002**: Chosen Roaring Bitmap indexing over B-tree secondary indexes (Section 13.4).
- **ADR-0003**: Chosen `rayon` for CPU-bound parallelism and confined `tokio` to orchestration only (Section 34).
- **ADR-0004**: Rejected dynamic plugin loading in favor of compile-time registration (Section 22).
- **ADR-0005**: Rejected ML/heuristic-based triage in favor of fully declarative rules (Section 20.1).
- **ADR-0006**: Chosen hash join as default correlation strategy, sort-merge as planner fallback (Section 19.3).
- **ADR-0007**: Chain of custody provides tamper-evidence, not tamper-prevention, against a malicious operator (Section 29.3).

New architectural decisions must be recorded as a new ADR entry (in `docs/adr/`) before implementation, following the standard Context/Decision/Consequences format; this PRD is updated to reference the ADR, not silently rewritten.

## 42. Coding Standards & Rust Guidelines

### 42.1 Edition & Toolchain
Rust 2021 edition, pinned MSRV (Minimum Supported Rust Version) tracked in `rust-toolchain.toml`, updated deliberately (not automatically) alongside a documented compatibility review.

### 42.2 Unsafe Code Policy
`unsafe` is permitted only for: SIMD intrinsics not otherwise exposed safely, memory-mapped file access (Section 35.2), and FFI where unavoidable. Every `unsafe` block requires an adjacent `// SAFETY:` comment documenting the invariant relied upon, is covered by a dedicated unit test, and is included in the fuzz corpus where it touches evidence-derived data (Section 27.3). `#![forbid(unsafe_code)]` is the default at the crate level for every crate except the small, explicitly-named set that requires it (`cfe-storage`'s mmap module, `cfe-query`'s SIMD kernel module).

### 42.3 Lints
Workspace-wide `Cargo.toml` `[workspace.lints]` deny: `clippy::unwrap_used`, `clippy::expect_used`, `clippy::panic` (outside `#[cfg(test)]`), and `clippy::todo`/`clippy::unimplemented` (enforcing the "no placeholder/TBD" requirement at the code level, mirroring this document's own quality bar).

### 42.4 Style
Formatting via `rustfmt` with the repository's `rustfmt.toml` enforced in CI (unformatted code fails CI, Section 45); public API documentation via `///` doc comments is required (enforced via `#![warn(missing_docs)]` on every library crate) for anything exported from `cfe-api`.

## 43. Cargo Workspace Structure

```
cfe/
├── Cargo.toml                  (workspace root)
├── crates/
│   ├── cfe-schema/             (Universal Artifact Schema, shared types — Section 11.1)
│   ├── cfe-acquire/            (acquisition, hashing, custody — Section 12)
│   ├── cfe-parsers/            (parser trait + all artifact parsers — Section 16, 17)
│   ├── cfe-storage/            (Arrow/Parquet, dictionary + bitmap index — Section 13)
│   ├── cfe-query/              (planner, optimizer, SIMD exec — Section 14, 15)
│   ├── cfe-timeline/           (Section 18)
│   ├── cfe-correlate/          (Section 19)
│   ├── cfe-rules/              (Section 20)
│   ├── cfe-report/             (Section 21, 38)
│   ├── cfe-orchestrator/       (Section 11.2, 34)
│   ├── cfe-api/                (stable public library surface — Section 31)
│   ├── cfe-cli/                (Section 23)
│   ├── cfe-gui/                (Section 24, optional/independent release cadence)
│   └── cfe-bench/              (Section 26.3, dev-only, not published)
└── docs/
    ├── adr/                    (Section 41)
    └── parsers/                (per-artifact detailed specs, Section 17.4–17.6)
```

## 44. Dependency Policy

New dependencies require: (1) an active maintenance history (commits within the last 12 months), (2) no known unresolved RUSTSEC advisories (checked via `cargo-deny` in CI, Section 45), (3) a documented justification in the PR description explaining why the functionality cannot reasonably be implemented in-crate. Dependencies performing cryptography (`sha2`, `zstd`) must be widely-audited, established crates — no novel/unaudited cryptographic implementations. Native (non-pure-Rust, requiring a C toolchain) dependencies are avoided where a pure-Rust alternative of comparable quality exists, to keep cross-compilation (Windows/Linux/ARM64, Section 7) simple and to keep the trust/audit boundary within Rust's safety guarantees wherever possible.

## 45. CI/CD

CI (GitHub Actions or equivalent) runs on every pull request: `cargo fmt --check`, `cargo clippy --all-targets -- -D warnings`, `cargo test --workspace`, `cargo deny check`, parser fuzz testing at the PR-time budget (Section 27.3), and the determinism validation job (Section 27.4). Release candidates additionally run the full benchmark suite (Section 26.3) with regression gating and the longer nightly fuzz budget. Releases are tagged only after all of the above pass, and `CHANGELOG.md` is updated with any breaking API or storage-format changes per Section 31/36.3.

## 46. Documentation Standards

Every public API item has a `///` doc comment (Section 42.4). Every module documented per the mandatory template (Section 11). Every artifact parser documented per the mandatory template (Section 17.1). Every architectural decision recorded as an ADR (Section 41) before implementation. This PRD itself is the top-level index; section-specific deep-dive documents (e.g., `docs/parsers/windows/mft.md`) expand on, but never contradict, this document — a detected contradiction is a bug in the sub-document, to be fixed to match the PRD, not the reverse, unless resolved via a new ADR that also updates this PRD.

## 47. Contribution Guide

Contributors: (1) read this PRD in full before proposing architectural changes, (2) file/reference an ADR (Section 41) for any change to a documented design decision, (3) add fixtures and unit tests for any new parser following Section 17's template and Section 27's fuzzing requirement before merge, (4) ensure `cargo fmt`, `cargo clippy`, and the full test suite pass locally before opening a PR, (5) update this PRD in the same PR when a change alters documented behavior — PRD updates are not a follow-up task, they are part of the definition of done.

## 48. Glossary

| Term | Definition |
|---|---|
| UAS | Universal Artifact Schema — the normalized data model all parsed artifacts map into (Section 32). |
| Row group | A horizontal partition of a Parquet file, the unit of pruning and parallel processing (Section 13.3, 14.2). |
| Bitmap index | A Roaring-Bitmap-based secondary index mapping column values to row positions (Section 13.4). |
| Predicate pushdown | Evaluating filter conditions as early/cheaply as possible in the query pipeline (Section 15.4). |
| Projection pushdown | Reading only the columns actually needed for a query (Section 15.4). |
| Entity key | A typed identifier (process, session, device, file, network) used to correlate events across artifact types (Section 19.2). |
| Chain of custody | The tamper-evident, append-only log of every operation performed on evidence (Section 12.3). |
| CASE/UCO | Cyber-investigation Analysis Standard Expression / Unified Cyber Ontology — an interoperable forensic data exchange standard (Section 38.1). |
| Determinism | The guarantee that identical inputs and CFE version always produce byte-identical outputs (Section 7). |
| ADR | Architecture Decision Record — the required format for documenting and superseding design decisions (Section 41). |

---

*End of document. This PRD is the single source of truth for the Columnar Forensics Engine project. Any implementation, pull request, or generated code that contradicts a section above is incorrect until this document is amended via a recorded ADR (Section 41).*
