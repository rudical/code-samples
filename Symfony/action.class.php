<?php

/**
 * Productcatagory actions.
 *
 * @package    COMPANY
 * @subpackage Productcatagory
 * @author     Rudie Shahinian
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProductcatagoryActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->product_catagorys = Doctrine::getTable('ProductCatagory')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->product_catagory = Doctrine::getTable('ProductCatagory')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->product_catagory);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new ProductCatagoryForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new ProductCatagoryForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($product_catagory = Doctrine::getTable('ProductCatagory')->find(array($request->getParameter('id'))), sprintf('Object product_catagory does not exist (%s).', $request->getParameter('id')));
    $this->form = new ProductCatagoryForm($product_catagory);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($product_catagory = Doctrine::getTable('ProductCatagory')->find(array($request->getParameter('id'))), sprintf('Object product_catagory does not exist (%s).', $request->getParameter('id')));
    $this->form = new ProductCatagoryForm($product_catagory);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($product_catagory = Doctrine::getTable('ProductCatagory')->find(array($request->getParameter('id'))), sprintf('Object product_catagory does not exist (%s).', $request->getParameter('id')));
    $product_catagory->delete();

    $this->redirect('Productcatagory/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $product_catagory = $form->save();

      $this->redirect('Productcatagory/edit?id='.$product_catagory->getId());
    }
  
