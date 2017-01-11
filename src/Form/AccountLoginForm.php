<?php
namespace Boxspaced\CmsAccountModule\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Filter;

class AccountLoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('account-login');

        $this->setAttribute('method', 'post');
        $this->setAttribute('accept-charset', 'UTF-8');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return void
     */
    protected function addElements()
    {
        $element = new Element\Hidden('redirect');
        $this->add($element);

        $element = new Element\Text('username');
        $element->setLabel('Username');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Password('password');
        $element->setLabel('Password');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Submit('login');
        $element->setValue('Login');
        $this->add($element);
    }

    /**
     * @return AccountLoginForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name'     => 'redirect',
            'allow_empty' => true,
        ]);

        $inputFilter->add([
            'name'     => 'username',
            'filters'  => [
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StringTrim::class],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'password',
            'filters'  => [
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StringTrim::class],
            ],
        ]);

        return $this->setInputFilter($inputFilter);
    }

}
