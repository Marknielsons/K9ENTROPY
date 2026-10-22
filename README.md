# K9ENTROPY

K9ENTROPY is an open-source research prototype for exploring whether non-invasive EEG measurements from a living dog could contribute additional, independently validated randomness to computer security systems.

## Features

- Dark responsive control dashboard with four live signal channels
- Synthetic EEG-style visualization for testing without a dog or hardware
- Local-only, authenticated EEG adapter ingest endpoint (visualization only)
- A demonstrator for the OS cryptographic random generator
- Health/status API with explicit security and validation disclosures
- Zero third-party runtime dependencies (PHP 8.2+)
- Unit tests, GitHub Actions CI, and optional Docker startup

## Getting started

Requires PHP 8.2 or later.

```bash
php -S 127.0.0.1:8080 -t public public/router.php
```

Open **http://127.0.0.1:8080**. No actual brain recordings or hardware are required.

Or with Docker:

```bash
docker compose up --build
```

Run the checks:

```bash
php tests/run.php
find src public tests -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Endpoints

| Route | Method | Purpose |
| --- | --- | --- |
| `/api/health` | GET | Simple health check |
| `/api/status` | GET | Returns simulation or fresh unverified adapter signals and exact cryptographic disclosures |
| `/api/random?bytes=16` | GET | Demonstrates `random_bytes()` with 1–64 bytes, **not intended for secrets** |
| `/api/eeg` | POST | Ingests a 4-channel frame for display only, local requests with bearer token |

Example random sample:

```bash
curl 'http://127.0.0.1:8080/api/random?bytes=16'
```

This response is public by design, so **never use its output as a secret**. Secure server applications should call their own trusted CSPRNG, such as PHP `random_bytes()`, directly.

### Optional local adapter demo

Use two terminal sessions. Set the **same** long random token in both. In the first:

```bash
export K9_INGEST_TOKEN="$(php -r 'echo bin2hex(random_bytes(32));')"
php -S 127.0.0.1:8080 -t public public/router.php
```

In the second, set `K9_INGEST_TOKEN` to that same value, then:

```bash
python3 scripts/demo_feed.py
```

The script sends **synthetic** data to `/api/eeg`. The dashboard shows `EEG ADAPTER · UNVERIFIED`, which indicates ingestion from the adapter endpoint, **not** confirmation that the data came from an animal. Every frame overwrites the previous frame in `var/latest-eeg.json`; it is not a history database. The adapter expires after 10 seconds without new data. Real EEG hardware will require a device-specific interface, signal calibration, veterinary and animal-welfare review, and independent validation.

The ingest endpoint accepts exactly four channels (`CH1`–`CH4`) with 32–128 numeric samples each, all channel lengths equal, sample values within ±1000 (illustrative microvolt scale). It rejects callers outside the local machine and requires an environment token of at least 24 characters. Do not expose the local development server to untrusted networks or put the token in client-side JavaScript.

```json
{"channels":{"CH1":[...],"CH2":[...],"CH3":[...],"CH4":[...]}}
```

Replace `[...]` with 32–128 real numerical readings per channel.

## Design boundaries

```
   DOG EEG / SIGNAL ADAPTER                    OPERATING SYSTEM
              |                                      |
              v                                      v
   local token-protected endpoint              CSPRNG / random_bytes()
              |                                      |
              v                                      v
   private, overwritten display buffer         sample API (demo only)
              |
              v
         VISUALIZATION

           [ NO CONNECTION TO CRYPTO KEYS ]
```

**Why no EEG-to-key pipeline?** Brain signals contain correlations and noise, can be affected by sensors or external stimuli, and do not establish a lower bound on unpredictable bits merely because the waveform looks noisy. Cryptographic entropy requires a defensible threat model, source characterization, conservative min-entropy estimation, health tests, a properly constructed conditioning function and CSPRNG integration, and independent review. NIST SP 800-90B and SP 800-90C are useful standards to study.

Even after future validation, EEG should only supplement an independently secure generator. Loss of signal, sleep, headset removal, or malicious sensor data must never weaken existing security.

## Real-world research roadmap

1. Obtain veterinary guidance and informed owner consent; prioritize the dog's comfort and use non-invasive recordings only.
2. Select a canine-compatible EEG device and confirm legal access to its raw samples and sampling timestamps.
3. Document electrode placements, sampling rate, artifact handling, and physical manipulation risks.
4. Collect labeled data securely, with minimal retention and no public raw EEG publication without consent.
5. Build **offline** evaluation tools for independence, correlations, adversarial influence, and conservative min-entropy claims. A randomness test alone does not establish cryptographic entropy.
6. Review a secure conditioning/mixing design with independent cryptographers *before* any production use.

## File layout

```
assets/             Original high-resolution ASCII dog artwork
public/             Dashboard, optimized dog mark, favicon, API router
src/                Signal validation, temporary frame buffer, simulation
scripts/demo_feed.py Synthetic local adapter example
var/                Private overwritten signal buffer; contents ignored by Git
.github/workflows/  PHP lint and tests
```

## Stack

PHP 8.2+, vanilla JavaScript, CSS, HTML Canvas. No cloud credentials and no external APIs are required to run the prototype. The dashboard optionally loads web fonts, but still works with system fonts offline.

## License

MIT. See [LICENSE](LICENSE).
