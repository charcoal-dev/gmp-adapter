# Charcoal GMP Adapter

[![MIT License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

The GMP lib is a thin, pragmatic wrapper around PHP’s ext-gmp, giving you a familiar BigInteger-style API plus clean
byte conversions. It standardizes import/export via gmp_import()/gmp_export() with explicit 
ByteOrder handling (big/little), fixed-width padding, and constant-time equals() for sensitive paths. 
The companion GmpByte enum captures common widths (8/16/32/64) and provides tight
pack/unpack helpers for U64 using GMP, mirroring the Buffers’ U8/U16/U32 routines. Together they let you move between
integers and raw bytes without surprises—no hidden endianness, no accidental truncation, and no dependency leakage into
the Buffers layer.

For detailed information, guidance, and setup instructions regarding this library, please refer to our official
documentation website:

[https://charcoal.dev/lib/gmp-adapter](https://charcoal.dev/lib/gmp-adapter)
