#!/bin/bash

# ================== #
# ===  COMMANDS  === #
# ================== #

case "$1" in
    "dc")
        shift 1

        docker-compose --version &> /dev/null
        if [[ $? == "0" ]]; then
          dc="docker-compose"
        fi

        docker compose version &> /dev/null
        if [[ $? == "0" ]]; then
          dc="docker compose"
        fi

        if [[ -z "${dc}" ]]; then
            echo "Neither docker-compose nor docker compose seems to be installed."
            exit 1
        fi

        ${dc} $@
    ;;

    "configure")
        [[ -f docker-compose.yml ]] || cp docker/docker-compose.yml.example docker-compose.yml
    ;;

    "attach-to-shell")
        SERVICE="$2"
        USER="$3"

        [[ -z ${SERVICE} ]] && SERVICE="php"
        [[ -z ${USER} ]] && USER=$UID

        ./cmd.sh dc exec -u $USER ${SERVICE} sh
    ;;

    "composer")
        shift 1;

        ./cmd.sh dc exec --user=$UID php php -d memory_limit=-1$ /usr/local/bin/composer ${@}
    ;;

    *)
        echo "Unknown command ${@}"
    ;;
esac
