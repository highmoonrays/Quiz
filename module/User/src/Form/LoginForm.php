<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Filter\StringToLower;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

class LoginForm extends Form
{
    /**
     * @param       $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('sign_in');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type'       => Email::class,
                'name'       => 'email',
                'options'    => [
                    'label' => 'Email',
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 128,
                    'pattern'      => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Enter email address.',
                    'placeholder'  => 'Email.'

                ]
            ]
        );

        $this->add(
            [
                'type'       => Password::class,
                'name'       => 'password',
                'options'    => [
                    'label' => 'Password'
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 25,
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Enter password.',
                    'placeholder'  => 'Enter your password.',
                ]
            ]
        );

        $this->add(
            [
                'type'       => Checkbox::class,
                'name'       => 'recall',
                'options'    => [
                    'label'              => 'Remember me?',
                    'label_attributes'   => [
                        'class' => 'custom-control-label'
                    ],
                    'use_hidden_element' => true,
                    'checked_value'      => '1',
                    'unchecked_value'    => '0'
                ],
                'attributes' => [
                    'value' => 0,
                    'id'    => 'recall',
                    'class' => 'form-control-input',
                ]
            ]
        );

        $this->add(
            [
                'type'    => Csrf::class,
                'name'    => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 600,
                    ]
                ]
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'account_login',
                'attributes' => [
                    'value' => 'Account Login',
                    'class' => 'btn btn-primary'
                ]
            ]
        );
    }

    /**
     * @return InputFilterInterface
     */
    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->filter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(
                [
                    'name' => 'email',
                    'required' => true,
                    'filters' => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class],
                        ['name' => StringToLower::class]
                    ],
                    'validators' => [
                        ['name' => NotEmpty::class],
                        [
                            'name' => StringLength::class,
                            'options' => [
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    StringLength::TOO_SHORT => 'Too short.',
                                    StringLength::TOO_LONG => 'Too long.',
                                ]
                            ]
                        ],
                        ['name' => EmailAddress::class],
                    ]
                ]
            );

            $inputFilter->add(
                [
                    'name' => 'password',
                    'required' => true,
                    'filters' => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class]
                    ],
                    'validators' => [
                        ['name' => NotEmpty::class],
                        [
                            'name' => StringLength::class,
                            'options' => [
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    StringLength::TOO_SHORT => 'Too short.',
                                    StringLength::TOO_LONG => 'Too long.',
                                ]
                            ]
                        ]
                    ]
                ]
            );

            $inputFilter->add(
                [
                    'name' => 'recall',
                    'required' => true,
                    'filters' => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class],
                        ['name' => ToInt::class]
                    ],
                    'validators' => [
                        ['name' => NotEmpty::class],
                        ['name' => IsInt::class],
                        [
                            'name' => InArray::class,
                            'options' => [
                                'haystack' => [0, 1],
                            ]
                        ]
                    ]
                ]
            );

            $inputFilter->add(
                [
                    'name'       => 'csrf',
                    'required'   => true,
                    'filters'    => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class],
                    ],
                    'validators' => [
                        ['name' => NotEmpty::class],
                        [
                            'name' => \Laminas\Validator\Csrf::class,
                            'options' => [
                                \Laminas\Validator\Csrf::NOT_SAME => 'Please refill the form.',
                            ]
                        ]
                    ],
                ],
            );

            $this->filter = $inputFilter;
        }

        return $this->filter;
    }

}

