<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\TaskType;
use AppBundle\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'Title',
            'content' => 'Content',
        );

        $object = new Task();
        $object->setTitle($formData['title']);
        $object->setContent($formData['content']);

        $form = $this->factory->create(TaskType::class);

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object->getTitle(), $form->get('title')->getData());
        $this->assertEquals($object->getContent(), $form->get('content')->getData());
        
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}