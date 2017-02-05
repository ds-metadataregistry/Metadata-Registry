<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Request
 * @package App\Http\Requests
 */
abstract class Request extends FormRequest
{

    /**
     * @var string
     */
    protected $error = '';

    /**
     * @return \Illuminate\Http\RedirectResponse
   */
    public function forbiddenResponse()
    {
        if (empty($error)) {
            $this->error = trans('auth.general_error');
        }

        return redirect()->back()->withErrors($this->error);
    }
}