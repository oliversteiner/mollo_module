#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

#/ Usage:       increment_versions.sh
#/ Version:     1.0
#/ Forked from siddharthkrish/version.sh github
#/ Description: Script for increment versions based on regex="([0-9]+).([0-9]+).([0-9]+)"
#/ Examples:
#/   increment_versions.sh -f versions.json -t patch -p photos -v
#/
#/ Options:
#/   -h|--help              : Display this help message
#/   -v|--verbose           : Display DEBUG log messages
#/   -f|--files             : input: path of <version_files> in json format
#/   -t|--type-of-version   : Take this value major|minor|patch for increment respectively
#/                            the first|two|three versions numbers separate by point.
#/    -p|--package-name     : The name of package ex: package_name1
#/    -d|--dry-run          : Active dry-run mode, the json file isn't update.
#/
#/ See json format files
#/ {
#/   "versions": {
#/     "package_name1": "1.0.0",
#/     "package_name2" "2.0.0"
#/   }
#/ }
function usage() { grep '^#/' "$0" | cut -c4- ; exit 0 ; }

#######################################################
## LOGGING FRAMEWORK
readonly NORMAL="\\e[0m"
readonly RED="\\e[1;31m"
readonly YELLOW="\\e[1;33m"
readonly DIM="\\e[2m"
# shellcheck disable=SC2034
readonly BOLD="\\e[1m"
readonly LOG_FILE="/tmp/$(basename "$0").log"
function log() {
  ( flock -n 200
    color="$1"; level="$2"; message="$3"
    printf "${color}%-9s %s\\e[m\\n" "[${level}]" "$message" | tee -a "$LOG_FILE" >&2
  ) 200>"/var/lock/.$(basename "$0").log.lock"
}
function debug() { if [ "$verbose" = true ]; then log "$DIM"    "DEBUG"   "$*"; fi }
function info()  { log "$NORMAL" "INFO"    "$*"; }
function warn()  { log "$YELLOW" "WARNING" "$*"; }
function error() { log "$RED"    "ERROR"   "$*"; }
function fatal() { log "$RED"    "FATAL"   "$*"; exit 1 ; }

function source_defs {
    resource=$1
    if [ -f "$resource" ]; then
        # shellcheck source=_functions.sh
        # shellcheck disable=SC1091
        source "$resource"
    else
        # shellcheck source=_functions.sh
        # shellcheck disable=SC1091
        source "${0%/*}/.irun-resources/$resource"
    fi
}

#######################################################

function cleanup() {
    # Remove temporary files
    # Restart services
    # ...
    if [ -f "file.json.tmp" ]; then
        rm file.json.tmp
    fi

    return
}

function retrieve_version_number_of_package() {
    local files="$1"
    local package_n="$2"
    local version;version=$(jq ".versions.${package_n}" "${files}" |sed s/\"//g)
    echo "${version}"
}


if [[ "${BASH_SOURCE[0]}" = "$0" ]]; then
    trap cleanup EXIT

    # Parse command line arguments
    dry_run=false
    POSITIONAL=()
    verbose=false
    while [[ $# -gt 0 ]]; do
        key="$1"
        case $key in
            -h|--help)
            usage
            ;;
            -v|--verbose)
            declare -r verbose=true
            shift
            ;;
            -f|--files)
            declare versions_files="$2"
            shift
            shift
            ;;
            -t|--type-of-version)
            declare type_of_version="$2"
            shift
            shift
            ;;
            -p|--package-name)
            declare package_name="$2"
            shift
            shift
            ;;
            -d|--dry-run)
            declare dry_run=true
            shift
            ;;
            *)    # unknown option
            POSITIONAL+=("$1") # save it in an array for later
            shift # past argument
            ;;
        esac
    done
    if [ "${#POSITIONAL[@]}" -ne 0 ]; then
        set -- "${POSITIONAL[@]}" # restore positional parameters
    fi

    if [ ! -f "${versions_files}" ]; then
        fatal "Could not open file ${versions_files}: No such file or directory"
    fi
    declare regex="([0-9]+).([0-9]+).([0-9]+)"
    declare version;version=$(retrieve_version_number_of_package "${versions_files}" "${package_name}" )
    debug "Actual version of ${package_name}: ${version}"

    if [[ $version =~ $regex ]]; then
        declare major; major="${BASH_REMATCH[1]}"
        declare minor;minor="${BASH_REMATCH[2]}"
        declare patch;patch="${BASH_REMATCH[3]}"
    else
        error "the "
        usage
    fi

    case "${type_of_version}" in
        major)
        major=$((major + 1 ))
        ;;
        minor)
        minor=$((minor + 1 ))
        ;;
        patch)
        patch=$((patch + 1 ))
        ;;
        *)
        error "${package_name} have wrong type of version"
        usage
        ;;
    esac
    # echo the new version number
    info "New version of ${package_name}: ${major}.${minor}.${patch}"

    if [[ $dry_run == false ]]; then
        jq -e ".versions.${package_name} =  \"${major}.${minor}.${patch}\"" "${versions_files}" > file.json.tmp
        cp file.json.tmp "${versions_files}"
    fi
fi
