<?php
namespace CSS;

class StyleRule
{
    /**
     * Human description
     */
    protected $description;

    /**
     * CSS selector associated with annotation
     */
    protected $cssSelector;

    /**
     * Some property that can be filled using the annotation data
     */
    protected $matchSelector;

    /**
     * Some property that can be filled using the annotation data
     */
    protected $conflictWith;

    /**
     * Constructor
     *
     * @param string: $cssSelector
     */
    public function __construct($cssSelector)
    {
        $this->cssSelector = $cssSelector;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string: $$name
     * @return StyleRule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get cssSelector
     *
     * @return string
     */
    public function getCssSelector()
    {
        return $this->cssSelector;
    }

    /**
     * Set cssSelector
     *
     * @param string: $cssSelector
     * @return StyleRule
     */
    public function setCssSelector($cssSelector)
    {
        $this->cssSelector = $cssSelector;

        return $this;
    }

    /**
     * Get matchSelector
     *
     * @return string
     */
    public function getMatchSelector()
    {
        return $this->matchSelector;
    }

    /**
     * Set matchSelector
     *
     * @param string: $$matchSelector
     * @return StyleRule
     */
    public function setMatchSelector($matchSelector)
    {
        $this->matchSelector = $matchSelector;

        return $this;
    }

    /**
     * Get conflictWith
     *
     * @return string
     */
    public function getConflictWith()
    {
        return $this->conflictWith;
    }

    /**
     * Set conflictWith
     *
     * @param string: $conflictWith
     * @return StyleRule
     */
    public function setConflictWith($conflictWith)
    {
        $this->conflictWith = $conflictWith;

        return $this;
    }
}