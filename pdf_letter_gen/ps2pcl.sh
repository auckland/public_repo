#!/bin/sh
gs -dBATCH -dSAFER -dQUIET -dNOPAUSE -sDEVICE=laserjet -sPAPERSIZE=a4 -sOutputFile=- - 2>/dev/null
