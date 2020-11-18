estcequecestbientotleweekend.fr
==============

Ok, the code sucks. But I was drunk when I did it.

### Start
```bash
$ pipenv --three install
$ echo "127.0.0.1 weekend.test" | sudo tee -a /etc/hosts
```

### Start
```bash
$ pipenv shell
$ inv start
```

### Builder


```bash
inv builder
```

### Deploy
```
$ fab prod deploy
```
