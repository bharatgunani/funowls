#!/bin/bash

commitId=$1

RESPONSE=$(curl http://ops101.sj2.cj.com:12302/deployable/${commitId} 2>/dev/null )
DEPLOYABLE="$(echo "$RESPONSE" | jq -r '.deployable')"

if [[ "$DEPLOYABLE" == true ]]; then
    echo "true"
else
    echo "false"
fi