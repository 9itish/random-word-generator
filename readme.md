# Random Dictionary Words

This is a neat random word generator that I created to output random dictionary words from the English language.

I have written the code in a way that will let users output random words in any language they want as long as the database has those words.

Currently, the API only supports English. However, I am planning to add more words from other languages to the database.

You can clone the repository to set up your own random word generator or you can simply use the one I have created at https://hellonitish.com/random-words/

## Usage

You can get a random word by making a GET request to `get.php`.

In my case, the JSON response was:

```json
{
  "words": [
    "distrait"
  ]
}
```

### Get Multiple Words

An application or project that you are working on might require you to get multiple words at once. Use the `count` parameter in such cases.

```
get.php?count=10
```

I got these 10 words back:

```json
{
  "words": [
    "nodosous",
    "pennaceous",
    "biolytic",
    "undwellable",
    "achromaticity",
    "ziega",
    "lisping",
    "comparation",
    "epicondyle",
    "reerect"
  ]
}
```


### Words From a Specific Category

At the moment, you can request words from 7 different categories &mdash; adjective, adverb, noun, verb, conjunction, preposition, fruit.

```
get.php?category=verb
```

Here is the response I got:

```json
{
  "words": [
    "chant"
  ]
}
```

### Words of a Specific Length

Provide a `length` value if you want the words to have a specific number of characters. This

```
get.php?length=18
```

The `length` parameter tales precedence over `min-length` and `max-length`.

Here is the response I got:

```json
{
  "words": [
    "incommensurability"
  ]
}
```

### Words Above or Equal to a Minimum Length

If you are looking for words with at least a minimum number of characters, use the `min-length` parameter.

```
count=10&min-length=15
```

Here is the response I got:

```json
{
  "words": [
    "hyperoxymuriate",
    "intermetacarpal",
    "supersemination",
    "devitrification",
    "anthropomorphitism",
    "concupiscential",
    "superexcrescence",
    "hydrometrograph",
    "churchwardenship",
    "contraremonstrant"
  ]
}
```

### Words Below or Equal to a Maximum Length

If you are looking for words with at least a minimum number of characters, use the `min-length` parameter.

```
count=10&max-length=5
```

Here is the response I got:

```json
{
  "words": [
    "gnaw",
    "trade",
    "dink",
    "ling",
    "acton",
    "roial",
    "wipe",
    "dowl",
    "begod",
    "shrag"
  ]
}
```

### Words That Start With a Specific Sequence

You should use the `start` parameter to get words that start with a specific sequence of letters.

```
get.php?start=app
```

Here is the response I got:

```json
{
  "words": [
    "appeasable"
  ]
}
```

### Words That End With a Specific Sequence

You should use the `end` parameter to get words that start with a specific sequence of letters.

```
get.php?end=ate
```

Here is the response I got:

```json
{
  "words": [
    "particulate"
  ]
}
```

### Words That Include a Specific Sequence

If you just want a character sequence to be anywhere in the word, consider using the `include` parameter.

```
include=der&count=10
```

Here is the response I got:

```json
{
  "words": [
    "underclothes",
    "raider",
    "innholder",
    "rudderless",
    "epidermoid",
    "interpleader",
    "derainment",
    "undersoil",
    "derival",
    "squanderingly"
  ]
}
```

You can also provide a comma separated list of characters and sequences if you want all of them to be present in the word.

```
include=der,t&count=5
```

Here is the response I got:

```json
{
  "words": [
    "dittander",
    "derivation",
    "stenodermine",
    "thereunder",
    "underplot"
  ]
}
```

### Words that Exclude a Specific Sequence

You might occasionally want to get a response that excludes words with specific characters or sequences. use the `exclude` parameter to do so.

```
exclude=a,e,i&count=5
```

Here is the response I got:

```json
{
  "words": [
    "outcrop",
    "dorp",
    "fluff",
    "oxford",
    "thoroughly"
  ]
}
```

## Things to Keep in Mind

I created this database from the [Online Plain Text English Dictionary](https://www.mso.anu.edu.au/~ralph/OPTED/) which is in public domain. The dictionary itself is based on the 1913 US Webster's Unabridged Dictionary.

1. Some of the words in use at that time might no longer be in use today.
2. Words recently added to English language will not be in the database.
3. Use of words evolve. This means that some words which were used as a verb as well as noun in the past might be used only as a verb today etc.