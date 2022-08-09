<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\I18n\Filter\Alnum;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;
use User\Entity\User;
use User\Repository\UserRepository;

class RegistrationForm extends Form
{
    /**
     * @param UserRepository $userRepository
     * @param                $name
     * @param array          $options
     */
    public function __construct(
        private readonly UserRepository $userRepository,
        $name = null,
        array $options = [],
    ) {
        parent::__construct('new_account');
        $this->setAttribute('method', 'post');
        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'username',
                'options'    => [
                    'label' => 'Username'
                ],
                'attributes' => [
                    'required'    => true,
                    'size'        => 40,
                    'maxLength'   => 25,
                    'pattern'     => '^[a-zA-Z0-9]+$',
                    'data-toggle' => 'tooltip',
                    'class'       => 'form-control',
                    'title'       => 'Alphanumeric only.',
                    'placeholder' => 'Enter username.',
                ]
            ]
        );

        $this->add(
            [
                'type'       => Select::class,
                'name'       => 'gender',
                'options'    => [
                    'label'         => 'Gender',
                    'empty_option'  => 'Choose an option.',
                    'value_options' => User::GENDERS
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'custom-select',
                ]
            ]
        );

        $this->add(
            [
                'type'       => Email::class,
                'name'       => 'email',
                'options'    => [
                    'label' => 'Email Address'
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 128,
                    'pattern'      => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Provide a valid Email address.',
                    'placeholder'  => 'Enter email address.',
                ]
            ]
        );

        $this->add(
            [
                'type'       => DateSelect::class,
                'name'       => 'birthday',
                'options'    => [
                    'label'               => 'Select your Birthdate',
                    'create_empty_option' => true,
                    'max_year'            => date('Y') - 13,
                    'year-attributes'     => [
                        'class' => 'custom-select w-30'
                    ],
                    'month-attributes'    => [
                        'class' => 'custom-select w-30'
                    ],
                    'day-attributes'      => [
                        'class' => 'custom-select w-30',
                        'id'    => 'day'
                    ],
                ],
                'attributes' => [
                    'required' => true
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
                    'title'        => 'Please enter from 4 to 25 characters',
                    'placeholder'  => 'Enter your password.',
                ]
            ]
        );

        $this->add(
            [
                'type'       => Password::class,
                'name'       => 'confirm_password',
                'options'    => [
                    'label' => 'Confirm password'
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 25,
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Password does not match',
                    'placeholder'  => 'Enter your password again.',
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
                'name'       => 'create_account',
                'attributes' => [
                    'value' => 'Create Account',
                    'class' => 'btn btn-primary'
                ]
            ]
        );
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->filter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(
                [
                    'name'       => 'username',
                    'required'   => true,
                    'filters'    => [
                        ['name' => StripTags::class],
                        ['name' => StringTrim::class],
                        ['name' => Alnum::class]
                    ],
                    'validators' => [
                        ['name' => NotEmpty::class],
                        [
                            'name'    => StringLength::class,
                            'options' => [
                                'min'      => 1,
                                'max'      => 25,
                                'messages' => [
                                    StringLength::TOO_SHORT => [
                                        'The username is too short, expect at least 1 symbol.'
                                    ],
                                    StringLength::TOO_LONG  => [
                                        'The username is too long, expect at most 25 symbols.'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'    => \Laminas\I18n\Validator\Alnum::class,
                            'options' => [
                                'messages' => [
                                    \Laminas\I18n\Validator\Alnum::NOT_ALNUM =>
                                        [
                                            'Username must consists of alphanumeric symbols only.'
                                        ]
                                ]
                            ]
                        ],
                        [
                            'name'    => Callback::class,
                            'options' => [
                                'callback_params' => [
                                    $this->userRepository
                                ],
                                'callback'        => function (
                                    $value,
                                    UserRepository $userRepository,
                                ) {
                                    $existingUsername =
                                        $userRepository->findOneBy(
                                            ['username' => $value]
                                        );
                                    if ($existingUsername) {
                                        return false;
                                    }
                                    return $value;
                                }
                            ]
                        ]
                    ]
                ]
            );
            $this->filter = $inputFilter;
        }
        return $this->filter;
    }
}
