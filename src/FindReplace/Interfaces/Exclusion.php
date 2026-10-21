<?php

namespace Nails\FindReplace\Interfaces;

if (interface_exists(Exclusion::class, false)) {
    return;
}

interface Exclusion
{
    /**
     * Never searched. Entries are `table`, `table.*`, `table.column`, or a delimited PCRE pattern.
     *
     * @return list<string>
     */
    public function hidden(): array;

    /**
     * Searched and shown, never replaced. Same entry format as hidden().
     *
     * @return list<string>
     */
    public function readOnly(): array;
}
