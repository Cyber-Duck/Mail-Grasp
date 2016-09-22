<?php
namespace Cyberduck\MailGrasp\Support;

class Collection extends \Illuminate\Support\Collection
{
    public function search($value, $strict = false)
    {
        if ($strict) {
            return parent::search($value, $strict);
        }

        foreach ($this->items as $key => $item) {
            if ($item->match($value)) {
                return $key;
            }
        }

        return false;
    }
}
