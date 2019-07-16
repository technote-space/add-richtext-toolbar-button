#!/usr/bin/env bash

set -e

if [[ $# -lt 1 ]]; then
	exit 1
fi

current=$(cd $(dirname $0);
pwd)

SCRIPT_DIR=${1}
source ${SCRIPT_DIR}/variables.sh

cp -f ${current}/plugin.js ${GH_WORK_DIR}/
cp -f ${current}/plugin.scss ${GH_WORK_DIR}/
