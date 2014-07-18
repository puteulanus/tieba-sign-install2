#!/bin/bash

php "${OPENSHIFT_REPO_DIR}php/cron.php" ||
  gear restart --all-cartridges
