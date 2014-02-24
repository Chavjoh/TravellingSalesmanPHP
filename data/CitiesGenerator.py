# coding: latin-1

"""

	A quick script for generating travelling salesman problem.

	Usage : 
		CitiesGenerator.py <number> <file>

	Will generate <number> couple of numbers and put it in the format <file>
		c1 x1 y2
		c2 x2 y2
		...

	/!\ If <file> exists it will be overwritten.

""" 

import sys
from random import randint

MAX_X = MAX_Y = 500

try:
	# Get scripts parameters
	filename = sys.argv[2]
	number = int(sys.argv[1])
except:
	print (__doc__)
	sys.exit(1)

# Open file in write mode
f = open(filename, "w")

# Create set of data
for i in range(number):
	line = "c%d %d %d\n" % (i, randint(0,MAX_X), randint(0,MAX_Y))
	f.write(line)

f.close()