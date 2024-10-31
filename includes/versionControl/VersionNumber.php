<?php

declare(strict_types=1);

namespace PWP\includes\versionControl;

class VersionNumber
{
    private int $major;
    private int $minor;
    private int $patch;
    private ?string $rest;

    private function __construct(int $major, int $minor, int $patch, ?string $rest = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->rest = $rest;
    }

    /**
     * static factory function to create version number object from string.
     *
     * @param string $version number as string. MUST be semantic, and in the format of ```INT.INT.INT.STRING```, separated by periods 
     * @return VersionNumber
     */
    public static function from_string(string $version): self
    {
        $version = explode('.', $version, 4);
        return new VersionNumber((int)$version[0], (int)$version[1], (int)$version[2], isset($version[3]) ? $version[3] : '');
    }

    /**
     * static factory function to create version number object from integers
     *
     * @param integer $major
     * @param integer $minor
     * @param integer $patch
     * @param string $rest
     * @return VersionNumber
     */
    public static function from_ints(int $major, int $minor, int $patch, ?string $rest = null): self
    {
        return new VersionNumber($major, $minor, $patch, $rest);
    }

    public function is_newer_than(VersionNumber $other): bool
    {
        return $this->to_int() > $other->to_int();
    }

    public function is_older_than(VersionNumber $other): bool
    {
        return $this->to_int() < $other->to_int();
    }

    public function equals(VersionNumber $other): bool
    {
        return $this->to_int() === $other->to_int();
    }

    /**
     * comparison function for array sorting
     *
     * @param VersionNumber $other
     * @return integer ```-1``` if this version is OLDER than other, ```0``` if they MATCH, and ```1``` if this version is NEWER
     */
    public function compare(VersionNumber $other): int
    {
        if ($this->equals($other)) return 0;
        if ($this->is_newer_than($other)) return  1;
        return -1;
    }

    /**
     * helper function to return version number as an integer for easier comparison
     * 
     * resulting int will be of format ```Major * 10000 + Minor * 100 + Patch```
     * we do this to allow for extra padding when the version numbers reach double digits.
     * It is thus not recommended to go over double digits with version numbering
     *
     * @return integer
     */
    private function to_int(): int
    {
        return $this->major * 10000 + $this->minor * 100 + $this->patch;
    }

    public function __toString()
    {
        $str = "{$this->major}.{$this->minor}.{$this->patch}";

        if (is_null($this->rest) || $this->rest !== '') {
            return $str . ".{$this->rest}";
        }
        return $str;
    }
}
