# DecMcpe

[![Join the chat at https://gitter.im/PEMapModder/DecMcpe](https://badges.gitter.im/PEMapModder/DecMcpe.svg)](https://gitter.im/PEMapModder/DecMcpe?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
My own project that parses an objdump of libminecraftpe.so

How to use
===
```shell
mkdir in
cp $PATH_TO_LIBMCPESO in/libminecraftpe.so
objdump -C -D in/libminecraftpe.so > in/mpe.asm
php _DecMcpe.php
```

Now you will have a dump of packets at `out/pkdump.json`.
