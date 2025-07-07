#!/bin/bash

# This script deploys the application to a production server in Locaweb hosting.
rm -rf ../public_html/prontoatendimento/assets/
cp -pr public/assets ../public_html/prontoatendimento/assets