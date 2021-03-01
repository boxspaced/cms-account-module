<?php
namespace Boxspaced\CmsAccountModule\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Filter;
use Zend\Validator;

class ChangePasswordForm extends Form
{

    public function __construct()
    {
        parent::__construct('change-password');

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

        $element = new Element\Password('password');
        $element->setLabel('Password');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Password('confirmPassword');
        $element->setLabel('Confirm password');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Submit('submit');
        $element->setValue('Change Password');
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
            'name'     => 'password',
            'filters'  => [
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StringTrim::class],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'confirmPassword',
            'filters'  => [
                ['name' => Filter\StripTags::class],
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                [
                    'name'    => Validator\Identical::class,
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

        return $this->setInputFilter($inputFilter);
    }

}
