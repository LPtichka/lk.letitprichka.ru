<?php
namespace app\models\Builder;

class Suggestions
{
    /** @var array */
    private $suggestions;

    /**
     * @return Suggestion[]
     */
    public function build()
    {
        $result = [];
        if ($this->suggestions) {
            foreach ($this->suggestions as $suggestion) {
                $result[] = (new Suggestion($suggestion))->getSuggestion();
            }
        }

        return $result;
    }

    /**
     * @param array $suggestions
     * @return $this
     */
    public function setSuggestions(array $suggestions)
    {
        $this->suggestions = $suggestions;
        return $this;
    }
}