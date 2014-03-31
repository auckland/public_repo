#!/bin/sh -p

PROGNAME=${0##*/}

Usage()
{
	print -u2 -- "\nUsage: $0 PRINTER_NAME [FILENAME]\n"
	exit 1
}

[[ $# -gt 1 ]] || Usage

PRINTER=$1
FILE=$2

2>/dev/null /usr/local/ext/htmldoc/bin/htmldoc \
--webpage -t ps --size a4 --left 0pt \
--jpeg=100 --right 0pt --fontsize 6 $FILE | \
./ps2pcl.sh | \
lp -d $PRINTER -o passthru
