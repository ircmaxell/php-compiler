struct __ref__;

struct __ref__virtual;

struct __string__;

struct __ref__
{
  int refcount;
  int typeinfo;
};

struct __ref__virtual
{
  struct __ref__ __ref__count;
};

struct __string__
{
  struct __ref__ __ref__count;
  size_t size;
  char[1] value;
};

static size_t __string__size;
static struct __string__ * __string__constant_0;

static inline void
__ref__init (int __ref__init_0, struct __ref__virtual * __ref__init_1)
{
main:
  __ref__init_1->__ref__count.refcount = (int)(long long)1;
  __ref__init_1->__ref__count.typeinfo = __ref__init_0;
  return;
}

static inline void
__ref__addref (struct __ref__virtual * __ref__addref_0)
{
main:
  if ((bool)(__ref__addref_0->__ref__count.typeinfo & (int)(long long)1)) goto refcounted; else goto notcounted;

refcounted:
  __ref__addref_0->__ref__count.refcount += (int)(long long)1;
  return;

notcounted:
  return;
}

static inline void
__ref__delref (struct __ref__virtual * __ref__delref_0)
{
main:
  if (__ref__delref_0 == (struct __ref__virtual *)NULL) goto notcounted; else goto not_null;

not_null:
  if ((bool)(__ref__delref_0->__ref__count.typeinfo & (int)(long long)1)) goto refcounted; else goto notcounted;

refcounted:
  __ref__delref_0->__ref__count.refcount -= (int)(long long)1;
  if (__ref__delref_0->__ref__count.refcount <= (int)(long long)0) goto needsfree; else goto notcounted;

needsfree:
  (void)free (((void *)__ref__delref_0));
  return;

notcounted:
  return;
}

static inline void
__ref__separate (struct __ref__virtual * * __ref__separate_0)
{
main:
  if (*__ref__separate_0 == (struct __ref__virtual *)NULL) goto shortcircuit; else goto not_null;

not_null:
  if ((bool)((*__ref__separate_0)->__ref__count.typeinfo & (int)(long long)1)) goto refcounted; else goto copy_and_allocate;

refcounted:
  if ((*__ref__separate_0)->__ref__count.refcount <= (int)(long long)1) goto shortcircuit; else goto delref;

copy_and_allocate:
  switch ((*__ref__separate_0)->__ref__count.typeinfo & (int)(long long)1020) {default: goto default; case (int)4 ... (int)4: goto string_block;}

shortcircuit:
  return;

delref:
  (void)__ref__delref (((struct __ref__virtual *)*__ref__separate_0));
  goto copy_and_allocate;

default:
  return;

string_block:
  (void)__string__separate (__ref__separate_0);
  return;
}

extern int
memcpy (char * memcpy_0, const char * memcpy_1, size_t memcpy_2); /* (imported) */

extern int
memset (char * memset_0, char memset_1, size_t memset_2); /* (imported) */

extern void
free (void * free_0); /* (imported) */

extern void *
malloc (size_t malloc_0); /* (imported) */

extern void *
realloc (void * realloc_0, size_t realloc_1); /* (imported) */

extern int
printf (const char * printf_0); /* (imported) */

extern int
sprintf (char * sprintf_0, const char * sprintf_1); /* (imported) */

extern int
snprintf (char * snprintf_0, size_t snprintf_1, const char * snprintf_2); /* (imported) */

static inline struct __string__ *
__string__alloc (size_t __string__alloc_0)
{
  struct __string__ * result;

main:
  result = (struct __string__ *)malloc ((__string__size + __string__alloc_0));
  result->size = __string__alloc_0;
  (void)__ref__init (((int)(long long)5), ((struct __ref__virtual *)result));
  return result;
}

static inline struct __string__ *
__string__init (const char * __string__init_0, size_t __string__init_1)
{
  struct __string__ * result;

main:
  result = __string__alloc (__string__init_1);
  (void)memcpy (((char *)&result->value[((size_t)(long long)0)]), ((const char *)__string__init_0), __string__init_1);
  return result;
}

static inline struct __string__ *
__string__realloc (struct __string__ * __string__realloc_0, size_t __string__realloc_1)
{
  struct __string__ * result;

main:
  result = (struct __string__ *)realloc (((void *)__string__realloc_0), (__string__size + __string__realloc_1));
  result->size = __string__realloc_1;
  if (__string__realloc_0 == (struct __string__ *)NULL) goto is_null; else goto not_null;

is_null:
  (void)__ref__init (((int)(long long)5), ((struct __ref__virtual *)result));
  goto not_null;

not_null:
  return result;
}

static inline void
__string__separate (struct __ref__virtual * * __string__separate_0)
{
  struct __string__ * tmp;

main:
  tmp = __string__init (((const char *)(char *)&((struct __string__ *)*__string__separate_0)->value[((size_t)(long long)0)]), ((struct __string__ *)*__string__separate_0)->size);
  *__string__separate_0 = (struct __ref__virtual *)tmp;
  return;
}

extern size_t
strlen (const char * strlen_0); /* (imported) */

static inline long long
__string__strlen (struct __string__ * __string__strlen_0)
{
main:
  return (long long)__string__strlen_0->size;
}

extern void
__init__ ()
{
initblock:
  __string__size = (&(struct __string__ *)NULL[(size_t)1] & &(struct __string__ *)NULL[(size_t)1]) - ((struct __string__ *)NULL & (struct __string__ *)NULL);
  __string__constant_0 = __string__init ("Hello World\
", ((size_t)(long long)12));
  ((struct __ref__virtual *)__string__constant_0)->__ref__count.typeinfo &= (int)(long long)-2;
  return;
}

extern void
__shutdown__ ()
{
shutdownblock:
  (void)free (((void *)__string__constant_0));
  return;
}

extern void
internal_1 ()
{
block_0:
  (void)printf ("%.*s", __string__constant_0->size, ((char *)&__string__constant_0->value[((size_t)(long long)0)]));
  return;
}

extern void
main ()
{
main:
  (void)__init__ ();
  (void)internal_1 ();
  (void)__shutdown__ ();
  return;
}

