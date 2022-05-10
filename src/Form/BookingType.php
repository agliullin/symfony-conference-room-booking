<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Form for booking
 */
class BookingType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'required' => true,
                'attr' => array (
                    'class' => 'form-control form-select',
                ),
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotNull(),
                ],
                'class' => Room::class,
                'query_builder' => function (RoomRepository $repo) {
                    return $repo->createQueryBuilder('room')
                        ->orderBy('room.name', 'ASC');
                },
                'choice_label' => function ($room) {
                    return $room->getName();
                },
            ])
            ->add('color', ChoiceType::class, [
                'required' => true,
                'attr' => array (
                    'class' => 'form-control',
                ),
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotNull(),
                ],
                'choices'  => [
                    'Red' => '#d51e1e',
                    'Green' => '#4ecc48',
                    'Blue' => '#3223f1',
                ],
            ])
            ->add('datetime_range', TextType::class, [
                'required' => true,
                'attr' => array (
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datepicker',
                    'data-format' => 'dd-mm-yyyy HH:ii',
                ),
                'constraints' => array (
                    new Constraints\NotBlank(),
                    new Constraints\NotNull(),
                ),
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotNull(),
                ],
            ])
            ->add('bookingMembers', EntityType::class, [
                'required' => true,
                'attr' => array (
                    'class' => 'form-control',
                ),
                'class' => User::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->orderBy('user.name', 'ASC');
                },
                'choice_label' => function ($user) {
                    return $user->getName() . ' (' . $user->getEmail() . ')';
                },
                'by_reference' => false
            ])
        ;
    }

    /**
     * Configure
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
