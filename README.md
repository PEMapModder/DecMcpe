# DecMcpe
My own project that parses an objdump of libminecraftpe.so

How to use
===
```shell
mkdir in
cp $PATH_TO_LIBMCPESO in/libminecraftpe.so
objdump -C -D in/libminecraftpe.so > in/mpe.asm
php DecMcpe.php
```

Now you will have a dump of packets at `out/pkdump.json`.
