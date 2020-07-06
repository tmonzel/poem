<?php

namespace Poem\Actor;

interface Behavior {
    function prepareActions(ActionDispatcher $actions);
}