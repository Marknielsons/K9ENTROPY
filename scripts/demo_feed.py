#!/usr/bin/env python3
"""Optional LOCAL simulated adapter that sends synthetic traces to the PHP API.
No dog or EEG hardware is involved. Never use these samples for key generation.
"""
import json
import math
import os
import random
import time
import urllib.request

TOKEN = os.environ.get('K9_INGEST_TOKEN', '')
URL = os.environ.get('K9_INGEST_URL', 'http://127.0.0.1:8080/api/eeg')
if len(TOKEN) < 24:
    raise SystemExit('Set K9_INGEST_TOKEN to the same long value as the PHP server.')

print('Publishing synthetic 4-channel frames to', URL)
while True:
    t = time.monotonic()
    channels = {
        f'CH{c}': [round(24 * math.sin((i * .22) + t + c) + random.uniform(-5, 5), 3)
                  for i in range(96)]
        for c in range(1, 5)
    }
    req = urllib.request.Request(URL, data=json.dumps({'channels': channels}).encode(),
                                 headers={'Authorization': f'Bearer {TOKEN}',
                                          'Content-Type': 'application/json'},
                                 method='POST')
    with urllib.request.urlopen(req, timeout=3) as response:
        print('Frame accepted:', response.status)
    time.sleep(1)
