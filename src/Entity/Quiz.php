<?php
declare(strict_types=1);
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Result", mappedBy="quiz")
     */
    private $results;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="quizzes")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $usersNumber = 0;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Question", mappedBy="quizzes")
     */
    private $questions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $firstPlace;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $secondPlace;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $thirdPlace;


    public function __construct()
    {
        $this->results = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Result[]
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setQuiz($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->contains($result)) {
            $this->results->removeElement($result);
            // set the owning side to null (unless already changed)
            if ($result->getQuiz() === $this) {
                $result->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }



    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->addQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            $question->removeQuiz($this);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getUsersNumber(): int
    {
        return $this->usersNumber;
    }

    /**
     * @param int $usersNumber
     */
    public function setUsersNumber(int $usersNumber): void
    {
        $this->usersNumber = $usersNumber;
    }

    public function getFirstPlace(): ?int
    {
        return $this->firstPlace;
    }

    public function setFirstPlace(int $firstPlace): self
    {
        $this->firstPlace = $firstPlace;

        return $this;
    }

    public function getSecondPlace(): ?int
    {
        return $this->secondPlace;
    }

    public function setSecondPlace(?int $secondPlace): self
    {
        $this->secondPlace = $secondPlace;

        return $this;
    }

    public function getThirdPlace(): ?int
    {
        return $this->thirdPlace;
    }

    public function setThirdPlace(?int $thirdPlace): self
    {
        $this->thirdPlace = $thirdPlace;

        return $this;
    }

}
