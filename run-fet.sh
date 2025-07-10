#!/bin/bash

# Direktori di mana skrip ini berada
BASEDIR=$(dirname "$0")

# Atur path ke library Qt, diasumsikan berada di dalam folder fet-engine/lib
export LD_LIBRARY_PATH="$BASEDIR/fet-engine/lib"

# Jalankan executable FET dengan semua argumen yang dilewatkan ke skrip ini
"$BASEDIR/fet-engine/fet-cl" "$@"
