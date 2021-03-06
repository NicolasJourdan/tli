<?php

namespace Beaver;

use Beaver\Request\Request;

abstract class AbstractForm
{
    /**
     * @var array
     */
    private $config;

    /**
     * AbstractForm constructor.
     */
    public function __construct()
    {
        $this->config = $this->build();
    }

    /**
     * return the config of the form
     *
     * @return array
     */
    protected abstract function build(): array;

    /**
     * Check if form is submitted and valid
     * Returns an array containing the form keys and values
     *
     * @param Request $request
     *
     * @return array
     */
    public function validate(Request $request): array
    {
        $inputs = [];
        foreach ($this->config as $inputName => $regex) {
            $value = $request->getPostValue($inputName);
            if (!preg_match("/^$regex$/", $value)) {
                // invalid form
                return [];
            }
            $inputs[$inputName] = $value;
        }
        return $inputs;
    }
}
