#!/bin/sh

php $@ | jq .

return $?
