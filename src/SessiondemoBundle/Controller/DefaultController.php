<?php

namespace SessiondemoBundle\Controller;

use SessiondemoBundle\Entity\Message;
use SessiondemoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $user = $session->get('user');
        if (null == $user){
            return $this->redirectToRoute('add_user');
        }
        $user = $em->getRepository("SessiondemoBundle:User")->find($user->getId());
        $message = new Message();
        $form = $this->createFormBuilder($message)->add('message')->add('save', SubmitType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $message->setUser($user);
            $message->setLikes(0);
            $em->persist($message);
            $em->flush();
        }
        $messages = $em->getRepository('SessiondemoBundle:Message')->findAll();
        return $this->render('SessiondemoBundle:Default:index.html.twig',  [
            "messages"  => $messages,
            "user" => $user,
            "form" => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/add", name = "add_user")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addUserAction(Request $request)
    {
        $user = new User();
        /**
         * @var Form $form
         */
        $form = $this->createFormBuilder($user)->add('name')->add('save', SubmitType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $request->getSession()->set('user', $user);
            return $this->redirectToRoute("home");
        }
        return $this->render("@Sessiondemo/Default/addUser.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/list", name="list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMessageAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $messages = $em->getRepository('SessiondemoBundle:Message')->findAll();
            return $this->render('SessiondemoBundle:Default:messageList.html.twig',  [
                "messages" => $messages,
            ]);
        }
    }

}
