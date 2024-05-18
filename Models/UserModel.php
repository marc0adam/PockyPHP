<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

class UserModel implements \ArrayAccess {
    protected $_fields = [];


    public static function load(int $user_id): ?static {
        $data = querySingleResult('SELECT * FROM users WHERE user_id=?', [$user_id]);
        if (empty($data)) {
            return null;
        }

        $member = new static();
        $member->_fields = $data;
        return $member;
    }


    // ArrayAccess functions
    public function offsetExists(mixed $offset): bool {
        return array_key_exists(strval($offset), $this->_fields); // returns true even if value is null
    }
    public function offsetGet(mixed $offset): mixed {
        return $this->_fields[strval($offset)] ?? null;
    }
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->_fields[strval($offset)] = $value;
    }
    public function offsetUnset(mixed $offset): void {
        unset($this->_fields[strval($offset)]);
    }
}
