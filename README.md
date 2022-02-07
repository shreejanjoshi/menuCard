# menuCard
Build Restaurant Menu Card Application With Symfony 5

## Setup

If you want to download this project and to get it working, follow these steps:

**Download Composer dependencies**

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

* creat an account in https://mailtrap.io/ -> inbox -> SMTP setting -> integration click on symfony 5+ and copy the       Mailer_DNS
* write your database username and password to connect.
* Replace it in the env.
* After type this command : php bin/console doctrine:schema:update --force

**Start the Symfony web server**

## Hello
Hi there. In this project I made Menucard in Symfony5. My main goal from this project is to grow my knowledge in Symfony.
I gain my knowledge in Symfony by following the in Udemy
[SYMFONY 5](https://www.udemy.com/course/symfony-course/)

This project is Menu Card where clint can come here and order food. While employee can add dish, remove it and change the status of food (ready, cooking and open). If there is new employee than only old employee can make account for him. Clint can also send mail to us like saying need extra fries and so on.

In this project I want focus on backend so I just download the templete and with all html, css and javascript on it. But I still did few frontend part link build form.. with bootstrap in it.

### Controller

* Homecontroller
    - gets data and return two random dish

    ```php
        class HomeController extends AbstractController
        {
            #[Route('/', name: 'home')]
            public function index(DishRepository $dr): Response
            {
                $dishes = $dr->findAll();

                $random = array_rand($dishes, 2);

                return $this->render('home/index.html.twig', [
                    'dish1' => ($dishes[$random[0]]),
                    'dish2' => ($dishes[$random[1]]),
                ]);
            }
        }
    ```

* DishController
    - function to create data , delete and show

    ```php
        #[Route('/dish', name: 'dish.')]
        class DishController extends AbstractController
        {
            // /dish/dish
            #[Route('/', name: 'edit')]
            //in repo find-one-by get individuel data but want to store entire data store in array
            public function index(DishRepository $dr): Response
            {
                $dishes = $dr->findAll();

                return $this->render('dish/index.html.twig', [
                    'dishes' => $dishes
                ]);
            }

            #[Route('/create', name: 'create')]
            public function create(ManagerRegistry $doctrine, Request $request): Response
            {
                $dish = new Dish();

                //form
                $form = $this->createForm(DishType::class, $dish);
                //to send data to database
                $form->handleRequest($request);

                //if submit
                if($form->isSubmitted()){
                    //entity manager
                    $em = $doctrine->getManager();

                    //store image -- files where all files is store
                    $image = $request->files->get('dish')['attachment'];

                    if($image){
                        //file name can be same so better  take file and attach dynamic componet ___ guessClintExtentension which is pendant to the entire file name
                        $filename = md5(uniqid('', true)). '.'. $image->guessClientExtension();
                    }

                    $image->move(
                        //config servise.yaml paramerts
                        $this->getParameter('images_folder'),
                        $filename
                    );

                    //update image in database with pass to the filename
                    $dish->setImage($filename);

                    $em->persist($dish);
                    //to change in database
                    $em->flush();

                    return $this->redirect($this->generateUrl('dish.edit'));
                }

                //response
                return $this->render('dish/create.html.twig', [
                    'createForm' => $form->createView(),
                ]);
            }

            #[Route('/delete/{id}', name: 'delete')]
            public function delete($id, DishRepository $dr, ManagerRegistry $doctrine){
                //entity manager
                $em = $doctrine->getManager();
                $dish = $dr->find($id);
                $em->remove($dish);
                //to change in database
                $em->flush();

                //flash message
                $this->addFlash('success','Dish was removed successfully');

                return $this->redirect($this->generateUrl('dish.edit'));
            }

            #[Route('/show/{id}', name: 'show')]
            //we can do it like up or with param converter Dish $dish and also need package annotation
            public function show(Dish $dish){
                return $this->render('dish/show.html.twig', [
                    'dish' => $dish
                ]);
            }
        }
    ```

* MenuController
    - find all the dish and return it to menu twig

    ```php
        #[Route('/menu', name: 'menu')]
        public function menu(DishRepository $dr): Response
        {
            $dishes = $dr->findAll();

            return $this->render('menu/index.html.twig', [
                'dishes' => $dishes
            ]);
        }
    ```

* RegistrationController
    - new user can be added to database

      ```php
        class RegistrationController extends AbstractController
        {
            #[Route('/reg', name: 'reg')]
            public function reg(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
            {
                $user = new User();

                //form
                $regform = $this->createForm(RegistrationType::class, $user);
                //to send data to database
                $regform->handleRequest($request);

                //if submit
                if($regform->isSubmitted()){
                    //password hasher
                    $input = $regform->get('password')->getData();
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $input)
                    );

                    //entity manager
                    $em = $doctrine->getManager();

                    $em->persist($user);
                    //to change in database
                    $em->flush();

                    return $this->redirect($this->generateUrl('home'));
                }

                return $this->render('registration/index.html.twig', [
                    'regform' => $regform->createView()
                ]);
            }
        }

    ```
* SecurityController
    - by symfony cmd

    ```
    php bin/console make:auth
    ```
* OrderController
    - order can be add, remove and there is also status where only user(employee) can change this 

    ```php
        class OrderController extends AbstractController
        {
            #[Route('/order', name: 'order')]
            public function index(OrderRepository $or): Response
            {
                //find only order that matches certin criteria
                $order = $or->findBy([
                    'sit' => 'sit1'
                ]);

                return $this->render('order/index.html.twig', [
                    'ordering' => $order
                ]);
            }

            #[Route('/orders/{id}', name: 'orders')]
            public function order(ManagerRegistry $doctrine, Dish $dish){
                $order = new Order();
                $order->setSit('sit1');
                $order->setName($dish->getName());
                $order->setOrdernumber($dish->getId());
                $order->setPrice($dish->getPrice());
                $order->setStatus("open");

                //entity manager
                $em = $doctrine->getManager();
                $em->persist($order);
                $em->flush();

                $this->addFlash('order', $order->getName(). ' is added to order.');
                return $this->redirect($this->generateUrl('menu'));
            }

            #[Route("/status/{id},{status}", name: "status")]
            public function status(ManagerRegistry $doctrine, $id, $status){
                $em= $doctrine->getManager();
                $order = $em->getRepository(Order::class)->find($id);

                //change the value
                $order->setStatus($status);
                $em->flush();

                return $this->redirect($this->generateUrl('order'));
            }

            #[Route('/remove/{id}', name: 'remove')]
            public function delete($id, OrderRepository $or, ManagerRegistry $doctrine){
                //entity manager
                $em = $doctrine->getManager();
                $order = $or->find($id);
                $em->remove($order);
                //to change in database
                $em->flush();

                return $this->redirect($this->generateUrl('order'));
            }
        }
    ```

* MailerController
    - made the form inside the controller and if submit data can be send to us
    -[Send Emails](https://symfony.com/doc/current/mailer.html)

    ```php
        class MailerController extends AbstractController
        {
            #[Route('/mail', name: 'mail')]
            public function sendEmail(MailerInterface $mailer, Request $request): Response
            {
                $emailForm = $this->createFormBuilder()
                    ->add('message', TextareaType::class,[
                        'attr' => array('rows' => '5')
                    ])
                    ->add('send', SubmitType::class,[
                        'attr' => [
                            'class' => 'btn btn-outline-danger float-right'
                        ]
                    ])

                    ->getForm();

                $emailForm->handleRequest($request);

                if($emailForm->isSubmitted()){
                    $input = $emailForm->getData();
                    $text = ($input['message']);
                    $sit = 'sit1';

                    $email = (new TemplatedEmail())
                        ->from('sit1@menucard.wip')
                        ->to('waiter@menucard.wip')
                        ->subject('Message')

                        ->htmlTemplate('mailer/mail.html.twig')

                        ->context([
                            'sit' => $sit,
                            'text' => $text
                        ]);

                    $mailer->send($email);

                    $this->addFlash('message', 'Your Message has been send successfully');

                    return $this->redirect($this->generateUrl('mail'));
                }
                return $this->render('mailer/index.html.twig',[
                    'emailForm' => $emailForm->createView()
                ]);
            }
        }

    ```

### Enity

* Category.php
    - id

    - name

    - category
    ```php
        #[ORM\OneToMany(targetEntity:"App\Entity\Dish", mappedBy:"Category")]
    ```

* Dish.php
    - id

    - name

    - description

    - price

    - image

    - category
     ```php
        #[ORM\ManyToOne(targetEntity:"App\Entity\Category", inversedBy:"Dish")]
    ```

* Order.php
    - id

    - sit

    - ordernumber

    - price

    - status

    - name -> name of the dish

* User.php
    - id

    - username

    - roles

    - password

### Form

* DishType
    - form to create the disg

    ```php
        class DishType extends AbstractType
        {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                $builder
                    ->add('name')
                    //different heading
                    ->add('attachment', FileType::class, ['mapped' => false])
                    //->add('image', FileType::class)
                    ->add('Description')
                    //dropdown category entity
                    ->add('category', EntityType::class, [
                        'class' => Category::class
                    ])
                    ->add('price')
                    ->add('save', SubmitType::class)
                ;
            }

            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                    'data_class' => Dish::class,
                ]);
            }
        }
    ```

* RegistrationType
    - form to register the user

    ```php
        class RegistrationType extends AbstractType
        {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                $builder
                    ->add('username', TextType::class,[
                            'label' => 'Employee Username'
                        ])
                    ->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'required' => true,
                        'first_options' => ['label' => 'Password'],
                        'second_options' => ['label' => 'Repeat Password']
                    ])
                    ->add('register', SubmitType::class)
                ;
            }

            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                    'data_class' => User::class,
                ]);
            }
        }
    ```

### Templates

* base.html.twig
    - header, footer, nav, link to css and javscript

* Home

* Dish
    - create.html.twig
    - index.html.twig
    - show.html.twig

* mailer
    - index.html.twig
    - mailer.html.twig

* menu

* order

* registration

* security