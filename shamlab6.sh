i=3
while [ $i -le 100 ]
do
	isPrime=1
	j=2;
	while [ $j -le `expr $i / 2` ]
	do
		if [ `expr $i % $j` -eq 0 ]
			then
			isPrime=0   
		fi

		j=`expr $j + 1`
	done

	if [ $isPrime -eq 1 ]
		then
		echo $i 
	fi

	i=`expr $i + 1`
done
 