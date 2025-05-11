<?php

namespace GaiaBB;

class Convert
{
    private $fromDbHost;

    private $toDbHost;

    private $prgBar;

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function __construct(&$prgbar, $fromDb, $toDb)
    {
        $this->prgbar = $prgbar;
        $this->toDbHost = $toDb;
        $this->fromDbHost = $fromDb;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function close()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function init()
    {
        $this->isAuth();
        $this->disableBoards();
        $this->settings();
        $this->members();
        $this->forums();
        $this->threads();
        $this->posts();
        $this->polls();
        $this->ranks();
        $this->attachments();
        $this->addresses();
        $this->favorites();
        $this->subscriptions();
        $this->censors();
        $this->banned();
        $this->messages();
        $this->finish();
        return true;
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function isAuth()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function disableBoards()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function settings()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function members()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function forums()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function threads()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function posts()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function polls()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function ranks()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function attachments()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function addresses()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function favorites()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function subscriptions()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function censors()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function banned()
    {
    }

    /**
     * function() - short description of function
     *
     * TODO: Long description of function
     *
     * @param  $varname type,
     *                  what it does
     * @return type, what the return does
     */
    public function messages()
    {
    }

    public function finish()
    {
    }
}
