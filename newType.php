<?php

namespace ajf\newType;

/**
 * Valid characters in an identifier
 * Source: https://github.com/php/php-src/blob/baf97b1fcc0f8458955f33bcfd325e3130e1161f/Zend/zend_execute_API.c#L992
 * @ignore
 */
const VALID_TYPE_NAME_CHARS = "0123456789_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\177\200\201\202\203\204\205\206\207\210\211\212\213\214\215\216\217\220\221\222\223\224\225\226\227\230\231\232\233\234\235\236\237\240\241\242\243\244\245\246\247\250\251\252\253\254\255\256\257\260\261\262\263\264\265\266\267\270\271\272\273\274\275\276\277\300\301\302\303\304\305\306\307\310\311\312\313\314\315\316\317\320\321\322\323\324\325\326\327\330\331\332\333\334\335\336\337\340\341\342\343\344\345\346\347\350\351\352\353\354\355\356\357\360\361\362\363\364\365\366\367\370\371\372\373\374\375\376\377\\";

/**
 * These names are special, they're available globally in all namespaces.
 * This is used like a set, thus the null values.
 * @ignore
 */
const TYPE_HINT_NAMES = [
    'int' => null,
    'float' => null,
    'string' => null,
    'bool' => null,
    'array' => null,
    'callable' => null,
    // These aren't types yet, but could be in future, and they're currently
    // globally reserved. We include them for future-proofing purposes.
    // Source: http://php.net/manual/en/reserved.other-reserved-words.php
    'true' => null,
    'false' => null,
    'null' => null,
    'resource' => null,
    'object' => null,
    'mixed' => null,
    'numeric' => null,
];

/**
 * @ignore
 */
function isValidTypeName(string $name): bool {
    return strlen($name) && strlen($name) === strspn($name, VALID_TYPE_NAME_CHARS);
}

/**
 * Creates a class with a given name which wraps the given type.
 * The resulting class has a constructor which accepts a value of the type
 * it wraps, and an `unbox` method which returns the value wrapped by am
 * instance of the class.
 * @returns void
 * @param $newTypeName string The fully-qualified name of the new class
 * @param $wrappedType string The fully-qualified name of the wrapped type
*/
function newType(string $newTypeName, string $wrappedTypeName) /* : void */ {
    if (!isValidTypeName($newTypeName)) {
        throw new \InvalidArgumentException("Invalid type name \"$newTypeName\"");
    }
    if (!isValidTypeName($wrappedTypeName)) {
        throw new \InvalidArgumentException("Invalid type name \"$wrappedTypeName\"");
    }
    
    // We need to strip leading slashes from the class name, because class
    // declarations don't contain absolute names
    if ($newTypeName[0] === '\\') {
        $newTypeName = substr($newTypeName, 1);
    }

    // Since class declarations don't contain absolute names, we have to
    // find the unqualified name and containing namespace, if any
    $lastSlash = strrpos($newTypeName, '\\');
    if ($lastSlash === FALSE) {
        $namespace = '';
    } else {
        $namespace = 'namespace ' . substr($newTypeName, 0, $lastSlash) . ';';
        $newTypeName = substr($newTypeName, $lastSlash + 1);
    }

    // On the other hand, we need to have a leading slash for the wrapped
    // name, since our type hint must be absolute; 
    if ($wrappedTypeName[0] !== '\\' && !isset(VALID_TYPE_NAME_CHARS[$wrappedTypeName])) {
        $wrappedTypeName = '\\' . $wrappedTypeName;
    }

    $src = <<<PHP
        $namespace
        
        class $newTypeName
        {
            private \$value;
            public function __construct($wrappedTypeName \$value) {
                \$this->value = \$value;
            }
            public function unbox(): $wrappedTypeName {
                return \$this->value;
            }
        }
PHP;
    eval($src);
}
