TravellingSalesmanPHP
=====================

PHP implementation of genetic algorithms to solve travelling salesman

### Requirements

* PHP 5.3

### How to use

Select the set of data to use in **index.php** with
```
CityManager::getInstance()->loadFromFile('data/pb_circle.txt');
```

And run the script.

### Data set generator

To generate set of data, use the python script **data/CitiesGenerator.py** :
```
CitiesGenerator.py <number> <file>
```
Where
* ```<number>``` is the number of city to create in the set
* ```<file>``` is the file that contains the set of data 

### Feedback

Don't hesitate to fork this project, improve it and make a pull request.

### License

This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/ or send a letter to Creative Commons, 444 Castro Street, Suite 900, Mountain View, California, 94041, USA.
