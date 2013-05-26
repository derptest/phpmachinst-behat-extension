<?php
/**
 * Copyright (c) 2013 Adam L. Englander
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace DerpTest\Behat\MachinistExtension\Context\ClassGuesser;

use Behat\Behat\Context\ClassGuesser\ClassGuesserInterface;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Context class guesser for the Machinist extension
 */
class MachinistContextClassGuesser implements ClassGuesserInterface
{

    /**
     * Tries to guess context classname.
     *
     * @return string
     */
    public function guess()
    {
        return 'DerpTest\Behat\MachinistExtension\Context\MachinistContext';
    }
}
