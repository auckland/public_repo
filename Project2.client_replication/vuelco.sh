#!/usr/bin/ksh

clientes_prs_org=CLIENTES_PRS.TXT.org
organizacion="Nuevo Banco Industrial de Azul"

cleanexit () {
	dialog --backtitle "$organizacion" --msgbox "El programa esta terminando..." 5 50
	clear
	exit 0
}

separarcorrer () {
	dialog --backtitle "$organizacion" --infobox "Procesando..." 5 50
	split -200 $clientes_prs_org CL_
	sleep 1
	for filename in CL_*
	do
#		mv ./$filename /tmp/CLIENTES_PRS_$filename.TXT
#»··»···cp /tmp/CLIENTES_PRS_$filename.TXT /tmp/CLIENTES_PRS.TXT
		mv ./$filename /tmp/CLIENTES_PRS.TXT
		php -f ./vuelco_clientes.php >> ./log_vuelco
	done
	dialog --backtitle "$organizacion" --msgbox "Operación está terminado..." 5 50
#	clear
#	exit 0
	menuinicio
}

#corrervuelco () {
#	for filename in /tmp/CLIENTES_PRS_*.*
#	php -f ./vuelco_clientes.php >> ./log_vuelco
#	dialog --backtitle "$organizacion" --msgbox "Operación está terminado..." 5 50
#	menuinicio
#}

menuinicio () {
	#limpiar tmp
	rm /tmp/menuitem.*
	dialog --backtitle "$organizacion" --title "Menú principal" --menu "Mover usando el [UP] [DOWN],[Enter] para seleccionar" 20 50 5 1 "Correr aplicación de vuelco" 2 "Salir" 2>/tmp/menuitem.$$

	menuitem=`cat /tmp/menuitem.$$`

	opcion=$?

	case $menuitem in
		1) separarcorrer;;
		2) cleanexit;;
	esac
}

inicio () {
	if [ ! -f .$clientes_prs_org ]
	then
		dialog --backtitle "$organizacion" --yesno "El archivo $clientes_prs_org existe. Desea de procesarlo?" 5 70
		if [ "$?" = "0" ]
		then
			menuinicio
		else
			cleanexit
		fi
	else
		dialog --backtitle "$organizacion" --infobox "El archivo $clientes_prs_org NO existe. Terminando..." 5 70
		cleanexit
#		exit 192
	fi
}
#	read iniciar

#	if [ $iniciar = "s" ]
#	then
#		separarmover
#	else	
#			if [ $iniciar = "S" ]
#			then
#				separarmover
#			else
#				cleanexit
#			fi
#	fi

#	if [ $iniciar = 0 ]
#	then
#		separarmover
#	else
#		cleanexit
#	fi
#}

inicio
