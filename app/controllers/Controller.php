<?php

namespace Bruder\Controller;

use Bruder\Model\User;
use Bruder\Utils\Arr;
use Bruder\Trait\ProcessesRequests;


class Controller
{
    use ProcessesRequests;

    /**
     * Keys that are valid for prequests even tho not explicitly
     * noted down in the controller
     *
     * @var array
     */
    protected $valid_passthrough_keys = ["habibi", "csrf_token"];

    /**
     * @var array|object
     */
    protected $params = [];

    /**
     * @var ?User
     */
    protected $current_user;

    /**
     * @param array $request - GET/POST/REQUEST
     */
    public function __construct(array $params = [])
    {
        /**
         * Set the input parameter.
         */
        $this->params = $params;
    }

    /**
     * Validates given params having keys specified and sets the
     * result to the protected params object to make it available in
     * the scope of this classes and those inheriting it.
     *
     * @param array $strict - Strictly necessary parameter.
     * @param array $optional - Will pass, but not necessary.
     * @param ?array $input_params
     * @return void
     */
    public function validate_params(
        array $strict,
        array $optional,
        ?array $input_params = null
    ) {
        /**
         * @var ?object
         */
        $this->params = $this->serialize_request_params(
            $strict,
            $input_params ?? $this->params,
            $optional
        );

        /**
         * If the param validation failed, we can die out of the
         * execution of any further code, since this will (hopefully)
         * always be the end.
         */
        if (!$this->params)
            die($this->error());
    }

    /**
     * Checks for given array keys being present in another array
     * and for array keys that are not allowed to be passed.
     *
     * @param array $necessary The keys needing to be present
     * @param array $post_params The array to check against
     * @param array $optional Let keys pass that are there but not filled
     * @return ?object
     */
    protected function serialize_request_params(
        array $necessary,
        array $post_params,
        array $optional = []
    ) {
        /**
         * @var array
         */
        $always_pass = ["csrf_token", "habibi"];

        // Check if all required parameters are set in the post request
        foreach ($necessary as $param)
            if (!isset($post_params[$param]))
                return null;

        // Check if any parameter in the post request is not in the required or optional arrays
        foreach ($post_params as $key => $value)
            if (
                !in_array($key, $necessary) &&
                !in_array($key, $optional) &&
                !in_array($key, $always_pass)
            )
                return null;

        /**
         * @var array
         */
        $serializedParams = [];

        /**
         * Sanitize all values recursively.
         */
        foreach ($post_params as $key => $value)
            if (is_array($value))
                $serializedParams[$key] = Arr::sanitize_special_chars($value);
            else
                $serializedParams[$key] =
                    is_numeric($value) && !str_starts_with($value, "0")
                    ? (int) $value
                    : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);

        /**
         * Append the current user to make it available in any controller.
         */
        // $serializedParams["current_user"] = $this->current_user;

        return (object) $serializedParams;
    }
}
