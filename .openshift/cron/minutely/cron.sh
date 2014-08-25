#!/bin/bash

php "${OPENSHIFT_REPO_DIR}php/cron.php" || ( echo 'Cron task failed.' >&2 )
