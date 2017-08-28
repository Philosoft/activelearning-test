<?php


namespace Todo\Controller;


use Doctrine\ORM\EntityManager;
use Todo\Entity\Task;
use Todo\Entity\User;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;

/**
 * Class TodoController
 * @package Todo\Controller
 *
 * @property EntityManager $entityManager
 * @property string $authToken
 * @property User|null $user
 * @property HydratorInterface $hydrator
 */
class TodoController extends AbstractRestfulController
{
    const MESSAGE_SUCCESS = "success";
    const MESSAGE_FAILURE = "failure";

    protected $entityManager = null;
    protected $authToken = null;
    protected $user = null;
    protected $hydrator = null;

    public function __construct(EntityManager $entityManager, HydratorInterface $hydrator) {
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->authToken = $this->params()->fromHeader("Authorization", null);
        if ($this->authToken !== null) {
            $this->authToken = $this->authToken->getFieldValue();
        }

        if (!$this->isValidAuthToken($this->authToken)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_401);
            throw new \Exception("not authorized");
        }

        return parent::onDispatch($e);
    }

    protected function isValidAuthToken($token = "")
    {
        if (empty($token)) {
            return false;
        }

        $this->user = $this->entityManager->getRepository("Todo\\Entity\\User")
            ->findOneBy([User::FIELD_NAME__AUTH_TOKEN => $token]);

        return $this->user !== null;
    }

    public function get($id)
    {
        /** @var Task $task */
        $task = $this->entityManager->find("Todo\\Entity\\Task", $id);
        if ($task !== null && $task->checkAuthToken($this->authToken)) {
            return new JsonModel($task->getDataAsArray());
        } else {
            return new JsonModel(["error" => "task with id {$id} not exists or u doesn't have sufficient rights"]);
        }
    }

    public function create($data)
    {
        $data["user"] = $this->user;
        if (!isset($data["created_at"])) {
            $data["created_at"] = "now";
        }
        $task = new Task();
        $this->hydrator->hydrate($data, $task);

        $message = self::MESSAGE_SUCCESS;

        try {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $message = self::MESSAGE_FAILURE;
        }

        return new JsonModel(["message" => $message]);
    }

    public function delete($id)
    {
        /** @var Task $task */
        $task = $this->entityManager->find("Todo\\Entity\\Task", $id);

        if ($task !== null && $task->checkAuthToken($this->authToken)) {
            $message = self::MESSAGE_SUCCESS;
            try {
                $this->entityManager->remove($task);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $message = self::MESSAGE_FAILURE;
            }

            return new JsonModel(["message" => $message]);
        } else {
            return new JsonModel(["error" => "there are no task with id {$id}"]);
        }
    }

    public function update($id, $data)
    {
        /** @var Task $task */
        $task = $this->entityManager->find("Todo\\Entity\\Task", $id);
        if ($task !== null && $task->checkAuthToken($this->authToken)) {
            $updateStatus = self::MESSAGE_SUCCESS;
            $this->hydrator->hydrate($data, $task);
            try {
                $this->entityManager->persist($task);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $updateStatus = "failure";
            }

            return new JsonModel(["message" => $updateStatus]);
        } else {
            throw new \Exception("task with #{$id} not found or u do not have sufficient rights");
        }
    }

    /**
     * Return all tasks for user as json
     * @return JsonModel
     */
    public function getList()
    {
        $taskCollection = $this->entityManager->getRepository("Todo\\Entity\\Task")
            ->findBy(["user" => $this->user]);
        if ($taskCollection !== null) {
            $taskCollection = array_map(
                function ($task) {
                    /** @var Task $task */
                    return $task->getDataAsArray();
                },
                $taskCollection
            );
        } else {
            $taskCollection = [];
        }

        return new JsonModel($taskCollection);
    }
}
